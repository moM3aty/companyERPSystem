<?php
// Path: app/Modules/Inventory/StockMovements/Application/StockService.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Application;

use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Modules\Inventory\Stock\Domain\Stock;
use App\Modules\Inventory\Stock\Domain\StockRepositoryInterface;
use App\Modules\Inventory\StockMovements\Domain\StockMovement;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;
use App\Modules\Inventory\StockMovements\Domain\StockMovementRepositoryInterface;
use App\Modules\Inventory\StockMovements\Domain\Events\StockUpdatedEvent;

/**
 * Enterprise Application Service: Stock Engine
 * الممر الإجباري لأي عملية تغيير في المخزون (صرف، إضافة، تسوية).
 * لا يمكن لأي موديول آخر التعديل المباشر في قاعدة البيانات لتجنب الكوارث المحاسبية.
 */
class StockService
{
    protected StockRepositoryInterface $stockRepo;
    protected StockMovementRepositoryInterface $movementRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        StockRepositoryInterface $stockRepo,
        StockMovementRepositoryInterface $movementRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->stockRepo = $stockRepo;
        $this->movementRepo = $movementRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    /**
     * تسجيل حركة مخزنية جديدة بأمان.
     *
     * @param int $productId
     * @param int $warehouseId
     * @param float $quantity
     * @param StockMovementType $type
     * @param string $referenceType
     * @param int $referenceId
     * @param int $companyId
     * @param int $userId
     * @param float $unitCost
     * @param string $notes
     * @return StockMovement
     * @throws BusinessException|\Throwable
     */
    public function recordMovement(
        int $productId,
        int $warehouseId,
        float $quantity,
        StockMovementType $type,
        string $referenceType,
        int $referenceId,
        int $companyId,
        int $userId,
        float $unitCost = 0.0,
        string $notes = ''
    ): StockMovement {
        
        if ($quantity <= 0.0) {
            throw new BusinessException("Movement quantity must be greater than zero.", 422);
        }

        return $this->transaction->execute(function () use (
            $productId, $warehouseId, $quantity, $type, $referenceType, $referenceId, $companyId, $userId, $unitCost, $notes
        ) {
            // 1. Lock the stock record strictly (Pessimistic Locking)
            $stock = $this->stockRepo->lockForUpdate($productId, $warehouseId, $companyId);

            if (!$stock) {
                // Initialize stock if it doesn't exist
                $stockData = [
                    'company_id'        => $companyId,
                    'product_id'        => $productId,
                    'warehouse_id'      => $warehouseId,
                    'quantity'          => 0.0,
                    'reserved_quantity' => 0.0,
                    'average_cost'      => 0.0,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
                $stockId = $this->stockRepo->create($stockData);
                
                $stockData['id'] = $stockId;
                $stock = new Stock($stockData);
            }

            // 2. Process logic based on Movement Type
            if ($type === StockMovementType::OUT) {
                if ($stock->getAvailableQuantity() < $quantity) {
                    throw new BusinessException("Insufficient stock in warehouse for Product ID: {$productId}. Available: {$stock->getAvailableQuantity()}.", 422);
                }
                $stock->subtractQuantity($quantity);
            } else {
                $stock->addQuantity($quantity);
                
                // Recalculate Moving Average Cost if it's an IN movement with cost
                if ($unitCost > 0.0) {
                    $currentQty = (float) $stock->getAttribute('quantity'); // Already added
                    $previousQty = $currentQty - $quantity;
                    $currentCost = (float) $stock->getAttribute('average_cost');
                    
                    $totalValue = ($previousQty * $currentCost) + ($quantity * $unitCost);
                    $newAverage = $totalValue / $currentQty;
                    
                    $stock->setAttribute('average_cost', round($newAverage, 4));
                }
            }

            $stock->setAttribute('last_movement_at', date('Y-m-d H:i:s'));

            // 3. Save the Stock record
            $this->stockRepo->update((int) $stock->getAttribute('id'), $stock->toArray());

            // 4. Record the Movement (Item Ledger)
            $movementData = [
                'company_id'     => $companyId,
                'product_id'     => $productId,
                'warehouse_id'   => $warehouseId,
                'movement_type'  => $type->value,
                'quantity'       => $quantity,
                'balance_after'  => (float) $stock->getAttribute('quantity'),
                'unit_cost'      => $unitCost > 0.0 ? $unitCost : (float) $stock->getAttribute('average_cost'),
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'notes'          => $notes,
                'created_by'     => $userId,
                'created_at'     => date('Y-m-d H:i:s')
            ];

            $movementId = $this->movementRepo->create($movementData);
            $movementData['id'] = $movementId;
            $movement = new StockMovement($movementData);

            // 5. Dispatch Event for other systems
            $this->eventBus->publish(new StockUpdatedEvent(
                $productId,
                $warehouseId,
                $type === StockMovementType::OUT ? -$quantity : $quantity,
                (float) $stock->getAttribute('quantity'),
                $type->value,
                $companyId
            ));

            return $movement;
        });
    }
}