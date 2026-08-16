<?php
// Path: app/Modules/Inventory/Transfers/Application/StockTransferService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Modules\Inventory\Transfers\Domain\StockTransferRepositoryInterface;
use App\Modules\Inventory\Transfers\Domain\Events\StockTransferCompletedEvent;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Stock Transfer Engine
 * يقوم بخصم البضاعة من المستودع الأول وإضافتها للمستودع الثاني بشكل ذري (Atomic).
 */
class StockTransferService
{
    protected StockTransferRepositoryInterface $transferRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;
    protected TenantContext $tenant;

    public function __construct(
        StockTransferRepositoryInterface $transferRepo,
        StockService $stockService,
        TransactionManager $transaction,
        EventBus $eventBus,
        TenantContext $tenant
    ) {
        $this->transferRepo = $transferRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
        $this->tenant = $tenant;
    }

    public function executeTransfer(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $itemsData, $userId) {
            
            $transferNo = $this->transferRepo->generateTransferNumber($companyId);

            // 1. إنشاء ترويسة التحويل
            $transferData = [
                'company_id'        => $companyId,
                'branch_id'         => $branchId,
                'transfer_no'       => $transferNo,
                'from_warehouse_id' => $headerData['from_warehouse_id'],
                'to_warehouse_id'   => $headerData['to_warehouse_id'],
                'transfer_date'     => $headerData['transfer_date'],
                'status'            => 'completed', // للتبسيط نعتبره اكتمل فوراً (لا يوجد In Transit)
                'created_by'        => $userId,
                'created_at'        => date('Y-m-d H:i:s')
            ];

            $transferId = $this->transferRepo->create($transferData);

            $processedItems = [];

            // 2. تحديث أرصدة المخازن عبر הـ Stock Engine لكل صنف
            foreach ($itemsData as $item) {
                $productId = (int) $item['product_id'];
                $qty = (float) $item['quantity'];

                // أ. حركة المنصرف (OUT) من المستودع المصدر
                $outMovement = $this->stockService->recordMovement(
                    $productId,
                    (int) $headerData['from_warehouse_id'],
                    $qty,
                    StockMovementType::OUT,
                    'stock_transfer',
                    $transferId,
                    $companyId,
                    $userId,
                    0.0,
                    "Transfer OUT to WH-{$headerData['to_warehouse_id']} (#{$transferNo})"
                );

                // ب. سحب التكلفة المتوسطة الحالية التي تم خصمها
                // (هذه التكلفة ضرورية لنقلها للمستودع الجديد حتى لا يفقد المخزون قيمته)
                $unitCost = (float) $outMovement->getAttribute('unit_cost');

                // ج. حركة الوارد (IN) إلى المستودع الهدف
                $this->stockService->recordMovement(
                    $productId,
                    (int) $headerData['to_warehouse_id'],
                    $qty,
                    StockMovementType::IN,
                    'stock_transfer',
                    $transferId,
                    $companyId,
                    $userId,
                    $unitCost,
                    "Transfer IN from WH-{$headerData['from_warehouse_id']} (#{$transferNo})"
                );

                $processedItems[] = [
                    'product_id' => $productId,
                    'quantity'   => $qty,
                    'unit_cost'  => $unitCost
                ];
            }

            // 3. إدخال السطور في جدول الـ Transfers
            $this->transferRepo->bulkInsertItems($transferId, $processedItems);

            // 4. إطلاق الحدث 
            $this->eventBus->publish(new StockTransferCompletedEvent($transferId, $companyId));

            return $transferId;
        });
    }
}