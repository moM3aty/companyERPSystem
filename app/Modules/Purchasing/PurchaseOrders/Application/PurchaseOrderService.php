<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Application/PurchaseOrderService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Modules\Purchasing\PurchaseOrders\Domain\PurchaseOrderRepositoryInterface;
use App\Modules\Purchasing\PurchaseOrders\Domain\Events\PurchaseOrderCreatedEvent;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Purchase Order
 * محرك المشتريات. يقوم بحساب المجاميع رياضياً على السيرفر ويرفض الاعتماد على حسابات الـ Frontend.
 */
class PurchaseOrderService
{
    protected PurchaseOrderRepositoryInterface $poRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;
    protected TenantContext $tenant;

    public function __construct(
        PurchaseOrderRepositoryInterface $poRepo,
        TransactionManager $transaction,
        EventBus $eventBus,
        TenantContext $tenant
    ) {
        $this->poRepo = $poRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
        $this->tenant = $tenant;
    }

    /**
     * إنشاء أمر شراء جديد.
     *
     * @param array $headerData
     * @param array $itemsData
     * @param int $userId
     * @return int
     * @throws BusinessException|\Throwable
     */
    public function createPurchaseOrder(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        // 1. الحساب الرياضي الآمن (Server-side Calculation)
        $subtotal = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $processedItems = [];

        foreach ($itemsData as $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['unit_price'];
            $discount = (float) ($item['discount_amount'] ?? 0.0);
            $tax = (float) ($item['tax_amount'] ?? 0.0);

            $lineBase = $qty * $price;
            $lineNet = $lineBase - $discount + $tax;

            $subtotal += $lineBase;
            $totalDiscount += $discount;
            $totalTax += $tax;

            $processedItems[] = [
                'product_id'      => (int) $item['product_id'],
                'description'     => $item['description'] ?? null,
                'quantity'        => $qty,
                'unit_price'      => $price,
                'discount_amount' => $discount,
                'tax_amount'      => $tax,
                'total'           => round($lineNet, 2)
            ];
        }

        $grandTotal = round($subtotal - $totalDiscount + $totalTax, 2);

        // 2. الحفظ داخل Transaction لضمان الـ ACID
        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $processedItems, $subtotal, $totalDiscount, $totalTax, $grandTotal, $userId) {
            
            $poToInsert = [
                'company_id'             => $companyId,
                'branch_id'              => $branchId,
                'po_number'              => $this->poRepo->generatePoNumber($companyId),
                'supplier_id'            => (int) $headerData['supplier_id'],
                'order_date'             => $headerData['order_date'],
                'expected_delivery_date' => $headerData['expected_delivery_date'] ?? null,
                'currency_id'            => (int) $headerData['currency_id'],
                'subtotal'               => $subtotal,
                'discount_total'         => $totalDiscount,
                'tax_total'              => $totalTax,
                'grand_total'            => $grandTotal,
                'status'                 => 'draft',
                'notes'                  => $headerData['notes'] ?? null,
                'created_by'             => $userId,
                'created_at'             => date('Y-m-d H:i:s')
            ];

            $poId = $this->poRepo->create($poToInsert);

            $this->poRepo->bulkInsertItems($poId, $processedItems);

            // 3. إطلاق الحدث للنظام
            $this->eventBus->publish(new PurchaseOrderCreatedEvent($poId, $companyId, $grandTotal));

            return $poId;
        });
    }
}