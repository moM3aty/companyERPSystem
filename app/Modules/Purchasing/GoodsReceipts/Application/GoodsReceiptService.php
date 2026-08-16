<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Application/GoodsReceiptService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Modules\Purchasing\GoodsReceipts\Domain\GoodsReceiptRepositoryInterface;
use App\Modules\Purchasing\GoodsReceipts\Domain\Events\GoodsReceiptProcessedEvent;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Goods Receipt Note (GRN)
 * يقوم بإدخال البضاعة للمستودع عبر الـ StockService بشكل محكم ومحمي.
 */
class GoodsReceiptService
{
    protected GoodsReceiptRepositoryInterface $receiptRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;
    protected TenantContext $tenant;

    public function __construct(
        GoodsReceiptRepositoryInterface $receiptRepo,
        StockService $stockService,
        TransactionManager $transaction,
        EventBus $eventBus,
        TenantContext $tenant
    ) {
        $this->receiptRepo = $receiptRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
        $this->tenant = $tenant;
    }

    public function processReceipt(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $itemsData, $userId) {
            
            // 1. إنشاء ترويسة إذن الاستلام
            $receiptData = [
                'company_id'        => $companyId,
                'branch_id'         => $branchId,
                'receipt_no'        => $this->receiptRepo->generateReceiptNumber($companyId),
                'purchase_order_id' => $headerData['purchase_order_id'] ?? null,
                'supplier_id'       => $headerData['supplier_id'],
                'receipt_date'      => $headerData['receipt_date'],
                'reference_doc'     => $headerData['reference_doc'] ?? null,
                'status'            => 'processed', // يتم إعتماده فوراً للتأثير في المخزن
                'received_by'       => $userId,
                'created_at'        => date('Y-m-d H:i:s')
            ];

            $receiptId = $this->receiptRepo->create($receiptData);

            // 2. إدخال السطور للمستند
            $this->receiptRepo->bulkInsertItems($receiptId, $itemsData);

            // 3. تحديث أرصدة المخازن عبر الـ Stock Engine لضمان مركزية العمليات
            foreach ($itemsData as $item) {
                $this->stockService->recordMovement(
                    (int) $item['product_id'],
                    (int) $item['warehouse_id'],
                    (float) $item['received_quantity'],
                    StockMovementType::IN,
                    'goods_receipt',
                    $receiptId,
                    $companyId,
                    $userId,
                    (float) ($item['unit_cost'] ?? 0.0),
                    "GRN #{$receiptData['receipt_no']}"
                );
            }

            // 4. إطلاق الحدث لتسجيل الاستحقاق المالي
            $this->eventBus->publish(new GoodsReceiptProcessedEvent($receiptId, $companyId, (int) $headerData['supplier_id']));

            return $receiptId;
        });
    }
}