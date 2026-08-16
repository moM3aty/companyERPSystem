<?php
// Path: app/Modules/Sales/Deliveries/Application/DeliveryNoteService.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Modules\Sales\Deliveries\Domain\DeliveryNoteRepositoryInterface;
use App\Modules\Sales\Deliveries\Domain\Events\DeliveryNoteProcessedEvent;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Delivery Note (Dispatch)
 * يقوم بخصم البضاعة من المستودع عبر الـ StockService بشكل محكم ومحمي.
 */
class DeliveryNoteService
{
    protected DeliveryNoteRepositoryInterface $deliveryRepo;
    protected StockService $stockService;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;
    protected TenantContext $tenant;

    public function __construct(
        DeliveryNoteRepositoryInterface $deliveryRepo,
        StockService $stockService,
        TransactionManager $transaction,
        EventBus $eventBus,
        TenantContext $tenant
    ) {
        $this->deliveryRepo = $deliveryRepo;
        $this->stockService = $stockService;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
        $this->tenant = $tenant;
    }

    public function processDelivery(array $headerData, array $itemsData, int $userId): int
    {
        $companyId = $this->tenant->requireTenant()->companyId;
        $branchId = $this->tenant->getBranchId();

        return $this->transaction->execute(function () use ($companyId, $branchId, $headerData, $itemsData, $userId) {
            
            // 1. إنشاء ترويسة إذن الصرف (التسليم)
            $deliveryData = [
                'company_id'     => $companyId,
                'branch_id'      => $branchId,
                'delivery_no'    => $this->deliveryRepo->generateDeliveryNumber($companyId),
                'sales_order_id' => $headerData['sales_order_id'] ?? null,
                'customer_id'    => $headerData['customer_id'],
                'delivery_date'  => $headerData['delivery_date'],
                'status'         => 'shipped', 
                'dispatched_by'  => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $deliveryId = $this->deliveryRepo->create($deliveryData);

            // 2. إدخال السطور
            $this->deliveryRepo->bulkInsertItems($deliveryId, $itemsData);

            // 3. خصم أرصدة المخازن عبر الـ Stock Engine لضمان الحماية من تجاوز الرصيد
            foreach ($itemsData as $item) {
                $this->stockService->recordMovement(
                    (int) $item['product_id'],
                    (int) $item['warehouse_id'],
                    (float) $item['delivered_quantity'],
                    StockMovementType::OUT,
                    'delivery_note',
                    $deliveryId,
                    $companyId,
                    $userId,
                    0.0, // التكلفة لا تُحدد عند الصرف، السيستم يستخدم المتوسط أو الـ FIFO داخلياً
                    "DEL #{$deliveryData['delivery_no']}"
                );
            }

            // 4. إطلاق الحدث لتسجيل تكلفة البضاعة المباعة (COGS) في المحاسبة
            $this->eventBus->publish(new DeliveryNoteProcessedEvent($deliveryId, $companyId, (int) $headerData['customer_id']));

            return $deliveryId;
        });
    }
}