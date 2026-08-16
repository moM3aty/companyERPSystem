<?php
// Path: app/Modules/Sales/CreditControl/Application/CreditControlService.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditControl\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Sales: Credit Control Service
 * صمام الأمان المالي. يتم استدعاؤه قبل إنشاء الفواتير أو أوامر البيع للتحقق من:
 * 1. الحد الائتماني للعميل (Credit Limit).
 * 2. الفواتير المتأخرة غير المسددة (Overdue Invoices).
 */
class CreditControlService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * فحص وتطبيق القيود الائتمانية على العميل قبل تمرير أمر بيع جديد.
     *
     * @param int $customerId
     * @param float $requestedAmount
     * @param int $companyId
     * @return void
     * @throws BusinessException
     */
    public function enforceCreditLimit(int $customerId, float $requestedAmount, int $companyId): void
    {
        // 1. جلب بيانات العميل الائتمانية
        $customer = $this->db->connection()->selectOne(
            "SELECT credit_limit FROM customers WHERE id = ? AND company_id = ?",
            [$customerId, $companyId]
        );

        if (!$customer) {
            throw new BusinessException("Customer not found.");
        }

        $creditLimit = (float) $customer['credit_limit'];

        // إذا كان الحد الائتماني 0، فهذا يعني أن العميل غير خاضع للتقييم (دفع نقدي فقط أو مسموح بلا حدود بناءً على سياسة الشركة).
        if ($creditLimit <= 0) {
            return;
        }

        // 2. حساب المديونية المفتوحة (الرصيد غير المسدد من الفواتير المرحلة)
        $arBalanceResult = $this->db->connection()->selectOne(
            "SELECT SUM(grand_total - paid_amount) as open_ar FROM sales_invoices 
             WHERE customer_id = ? AND status = 'posted'",
            [$customerId]
        );
        $openAr = (float) ($arBalanceResult['open_ar'] ?? 0.0);

        // 3. حساب الطلبات المعلقة (التي استهلكت حد الائتمان ولم تتحول لفاتورة بعد)
        $pendingOrdersResult = $this->db->connection()->selectOne(
            "SELECT SUM(grand_total) as pending_so FROM sales_orders 
             WHERE customer_id = ? AND status IN ('confirmed', 'processing', 'shipped')",
            [$customerId]
        );
        $pendingOrders = (float) ($pendingOrdersResult['pending_so'] ?? 0.0);

        // 4. التقييم النهائي
        $totalExposure = $openAr + $pendingOrders + $requestedAmount;

        if ($totalExposure > $creditLimit) {
            $availableCredit = max(0, $creditLimit - ($openAr + $pendingOrders));
            throw new BusinessException(
                "Credit Hold: Customer has exceeded their credit limit. Available credit: {$availableCredit}. Requested: {$requestedAmount}.",
                402 // Payment Required
            );
        }

        // 5. التحقق من وجود فواتير متأخرة جداً (مثلاً أكثر من 30 يوماً متأخرة)
        $overdueInvoices = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as cnt FROM sales_invoices 
             WHERE customer_id = ? AND status = 'posted' AND due_date < DATE_SUB(NOW(), INTERVAL 30 DAY) AND (grand_total - paid_amount) > 0",
            [$customerId]
        );

        if (((int) $overdueInvoices['cnt']) > 0) {
            throw new BusinessException(
                "Credit Hold: Customer has {$overdueInvoices['cnt']} severely overdue invoices. No new orders can be processed until payments are made.",
                402
            );
        }
    }
}