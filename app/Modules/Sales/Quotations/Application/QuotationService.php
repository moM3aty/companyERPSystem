<?php
// Path: app/Modules/Sales/Quotations/Application/QuotationService.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Application;

use App\Core\Database\TransactionManager;
use App\Core\Calculation\TotalCalculator;
use App\Core\Calculation\Percentage;
use App\Modules\Sales\Quotations\Domain\QuotationRepositoryInterface;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Quotation
 * يحسب عروض الأسعار بدقة، معتمداً على הـ TotalCalculator في הـ Core لضمان تطابق أرقام المبيعات في كل النظام.
 */
class QuotationService
{
    protected QuotationRepositoryInterface $quotationRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;

    public function __construct(
        QuotationRepositoryInterface $quotationRepo,
        TransactionManager $transaction,
        TenantContext $tenant
    ) {
        $this->quotationRepo = $quotationRepo;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
    }

    public function createQuotation(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $grandTotal = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            // استخدام حاسبة الـ Core الموحدة لضمان عدم وجود كسور خاطئة
            $calc = TotalCalculator::calculateLineItem(
                (float) $item['quantity'],
                (float) $item['unit_price'],
                (float) ($item['discount_amount'] ?? 0.0),
                new Percentage(0.0) // الضرائب الثابتة تُحسب لاحقاً بناءً على إعدادات الصنف، هنا للتبسيط נمرر 0
            );

            // إضافة الضريبة إن تم تمريرها صراحة من הـ Request للتبسيط
            $tax = (float) ($item['tax_amount'] ?? 0.0);
            $lineNetTotal = $calc['net_before_tax'] + $tax;

            $subtotal += $calc['gross_total'];
            $totalDiscount += $calc['discount'];
            $totalTax += $tax;
            $grandTotal += $lineNetTotal;

            $processedItems[] = [
                'product_id'      => (int) $item['product_id'],
                'description'     => $item['description'] ?? null,
                'quantity'        => $calc['quantity'],
                'unit_price'      => $calc['unit_price'],
                'discount_amount' => $calc['discount'],
                'tax_amount'      => $tax,
                'total'           => $lineNetTotal,
            ];
        }

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $totalDiscount, $totalTax, $grandTotal, $userId) {
            
            $quotationData = [
                'company_id'     => $companyId,
                'branch_id'      => $branchId,
                'quotation_no'   => $this->quotationRepo->generateQuotationNumber($companyId),
                'customer_id'    => $headerData['customer_id'],
                'quotation_date' => $headerData['quotation_date'],
                'valid_until'    => $headerData['valid_until'],
                'currency_id'    => $headerData['currency_id'],
                'subtotal'       => $subtotal,
                'discount_total' => $totalDiscount,
                'tax_total'      => $totalTax,
                'grand_total'    => $grandTotal,
                'status'         => 'draft',
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $quotationId = $this->quotationRepo->create($quotationData);

            $this->quotationRepo->bulkInsertItems($quotationId, $processedItems);

            return $quotationId;
        });
    }
}