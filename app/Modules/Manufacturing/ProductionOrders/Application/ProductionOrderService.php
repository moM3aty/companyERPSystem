<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Application/ProductionOrderService.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Application;

use App\Modules\Manufacturing\ProductionOrders\Domain\ProductionOrderRepositoryInterface;
use App\Modules\Manufacturing\ProductionOrders\Domain\Events\ProductionOrderCompletedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Core\Tenant\TenantContext;

/**
 * Enterprise Application Service: Production Order Engine
 * Controls the manufacturing lifecycle. Translates BOM definitions into actual material requirements based on the planned quantity.
 */
class ProductionOrderService
{
    protected ProductionOrderRepositoryInterface $orderRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected EventBus $eventBus;
    protected TenantContext $tenantContext;

    public function __construct(
        ProductionOrderRepositoryInterface $orderRepo,
        TransactionManager $transaction,
        DatabaseManager $db,
        EventBus $eventBus,
        TenantContext $tenantContext
    ) {
        $this->orderRepo = $orderRepo;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->eventBus = $eventBus;
        $this->tenantContext = $tenantContext;
    }

    /**
     * Plan and create a Production Order based on a specified BOM.
     * Calculates the exact raw materials needed, factoring in scrap percentages.
     *
     * @param array $data
     * @param int $userId
     * @return int The ID of the newly created Production Order
     * @throws BusinessException|\Throwable
     */
    public function createOrder(array $data, int $userId): int
    {
        $companyId = $this->tenantContext->requireTenant()->companyId;
        $branchId = $this->tenantContext->getBranchId();

        return $this->transaction->execute(function () use ($data, $companyId, $branchId, $userId) {
            
            $bomId = (int) $data['bom_id'];
            $plannedQty = (float) $data['planned_quantity'];

            // 1. Fetch BOM Details to determine multiplier
            $bom = $this->db->connection()->selectOne(
                "SELECT * FROM manufacturing_boms WHERE id = ? AND company_id = ? AND is_active = 1",
                [$bomId, $companyId]
            );

            if (!$bom) {
                throw new BusinessException("The selected BOM is invalid or inactive.", 422);
            }

            $bomBatchQty = (float) $bom['batch_quantity'];
            $multiplier = $plannedQty / $bomBatchQty; // e.g., We want 500, BOM is for 100. Multiplier = 5.

            // 2. Fetch BOM Items to calculate exact Material Requirements
            $bomItems = $this->db->connection()->select(
                "SELECT * FROM manufacturing_bom_items WHERE bom_id = ?",
                [$bomId]
            );

            $requiredMaterials = [];
            foreach ($bomItems as $item) {
                // Base required = Quantity defined in BOM * the batch multiplier
                $baseRequired = (float) $item['quantity'] * $multiplier;
                
                // Account for expected scrap/waste (e.g., 5% scrap means we need 1.05x the material)
                $scrapFactor = 1 + ((float) $item['scrap_percentage'] / 100);
                
                $requiredMaterials[] = [
                    'component_product_id' => $item['component_product_id'],
                    'required_quantity'    => round($baseRequired * $scrapFactor, 4),
                ];
            }

            // 3. Prepare Order Header
            $orderData = [
                'company_id'       => $companyId,
                'branch_id'        => $branchId,
                'bom_id'           => $bomId,
                'product_id'       => $bom['product_id'],
                'order_number'     => $this->orderRepo->generateOrderNumber($companyId),
                'planned_quantity' => $plannedQty,
                'produced_quantity'=> 0.00,
                'status'           => 'planned',
                'start_date'       => $data['start_date'],
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s'),
            ];

            // 4. Save Header and Line Items atomically
            return $this->orderRepo->saveWithItems($orderData, $requiredMaterials);
        });
    }

    /**
     * Complete the production order and trigger downstream Inventory/Accounting events.
     *
     * @param int $orderId
     * @param float $actualProducedQty
     * @return void
     * @throws BusinessException|\Throwable
     */
    public function completeOrder(int $orderId, float $actualProducedQty): void
    {
        $companyId = $this->tenantContext->requireTenant()->companyId;

        $this->transaction->execute(function () use ($orderId, $actualProducedQty, $companyId) {
            
            $this->orderRepo->setTenantId($companyId);
            // Uses FOR UPDATE implicitly via transaction and findOrFail if implemented securely, 
            // but we check status manually here.
            $order = $this->orderRepo->findOrFail($orderId);

            if ($order['status'] === 'completed') {
                throw new BusinessException("This production order is already marked as completed.", 409);
            }

            // 1. Update produced quantity and close the order
            $this->orderRepo->update($orderId, [
                'produced_quantity' => $actualProducedQty,
            ]);
            $this->orderRepo->markAsCompleted($orderId);

            // 2. Fire Event for Stock (Consumption & Receipt) & Accounting updates
            $this->eventBus->publish(new ProductionOrderCompletedEvent(
                $orderId, 
                $companyId, 
                (int) $order['product_id'], 
                $actualProducedQty
            ));
        });
    }
}