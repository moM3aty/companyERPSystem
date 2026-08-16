<?php
// Path: app/Modules/Sales/SalesOrders/Application/SalesOrderService.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Application;

use App\Core\Database\TransactionManager;
use App\Core\Calculation\TotalCalculator;
use App\Core\Calculation\Percentage;
use App\Modules\Sales\SalesOrders\Domain\SalesOrderRepositoryInterface;
use App\Core\Tenant\TenantContext;
use App\Modules\Sales\CreditControl\Application\CreditControlService;

/**
 * Enterprise Application Service: Sales Order
 * يقوم بحساب الأمر وتأكيده، مع تطبيق صارم للرقابة الائتمانية.
 */
class SalesOrderService
{
    protected SalesOrderRepositoryInterface $orderRepo;
    protected TransactionManager $transaction;
    protected TenantContext $tenant;
    protected CreditControlService $creditControl;

    public function __construct(
        SalesOrderRepositoryInterface $orderRepo,
        TransactionManager $transaction,
        TenantContext $tenant,
        CreditControlService $creditControl
    ) {
        $this->orderRepo = $orderRepo;
        $this->transaction = $transaction;
        $this->tenant = $tenant;
        $this->creditControl = $creditControl;
    }

    public function createOrder(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $grandTotal = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $calc = TotalCalculator::calculateLineItem(
                (float) $item['quantity'],
                (float) $item['unit_price'],
                (float) ($item['discount_amount'] ?? 0.0),
                new Percentage(0.0) 
            );

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

        // --- التحديث الجديد: فحص الحد الائتماني للعميل قبل فتح الـ Transaction ---
        $this->creditControl->enforceCreditLimit((int) $headerData['customer_id'], $grandTotal, $companyId);

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $totalDiscount, $totalTax, $grandTotal, $userId) {
            
            $orderData = [
                'company_id'     => $companyId,
                'branch_id'      => $branchId,
                'order_no'       => $this->orderRepo->generateOrderNumber($companyId),
                'customer_id'    => $headerData['customer_id'],
                'quotation_id'   => $headerData['quotation_id'] ?? null,
                'order_date'     => $headerData['order_date'],
                'delivery_date'  => $headerData['delivery_date'] ?? null,
                'currency_id'    => $headerData['currency_id'],
                'subtotal'       => $subtotal,
                'discount_total' => $totalDiscount,
                'tax_total'      => $totalTax,
                'grand_total'    => $grandTotal,
                'status'         => 'confirmed', // في الحقيقة يحتاج Workflow، للتبسيط سنعتبره مؤكداً
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $orderId = $this->orderRepo->create($orderData);
            $this->orderRepo->bulkInsertItems($orderId, $processedItems);

            return $orderId;
        });
    }
}