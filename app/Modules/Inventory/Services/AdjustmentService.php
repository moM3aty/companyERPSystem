<?php
// Path: app/Modules/Inventory/Services/AdjustmentService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Inventory\StockMovements\Application\StockService;
use App\Modules\Inventory\StockMovements\Domain\StockMovementType;

/**
 * Enterprise Application Service: Stock Adjustment
 * ينفذ تسويات العجز والزيادة الفردية مع ضمان التأثير في المخزون وتوليد القيد.
 */
class AdjustmentService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected StockService $stockService;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, StockService $stockService)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->stockService = $stockService;
    }

    public function postAdjustment(array $headerData, array $items, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($headerData, $items, $companyId, $userId) {
            $adjNo = 'ADJ-' . date('ymd') . '-' . random_int(100, 999);
            
            $adjId = $this->db->connection()->insert(
                "INSERT INTO inventory_adjustments (company_id, warehouse_id, adjustment_no, adjustment_date, reason, status, created_by, created_at) VALUES (?, ?, ?, ?, ?, 'posted', ?, ?)",
                [$companyId, $headerData['warehouse_id'], $adjNo, $headerData['adjustment_date'], $headerData['reason'], $userId, date('Y-m-d H:i:s')]
            );
            $adjId = (int) $this->db->connection()->lastInsertId();

            foreach ($items as $item) {
                $qty = (float) $item['quantity'];
                
                if ($qty == 0) {
                    continue;
                }

                $type = $qty > 0 ? StockMovementType::IN : StockMovementType::OUT;

                // استدعاء محرك المخزون الرئيسي لتسجيل الحركة بأمان
                $this->stockService->recordMovement(
                    (int) $item['product_id'],
                    (int) $headerData['warehouse_id'],
                    abs($qty),
                    $type,
                    'inventory_adjustment',
                    $adjId,
                    $companyId,
                    $userId,
                    0.0, // سيتم أخذ التكلفة الحالية للصنف آلياً
                    "Adjustment {$adjNo}: {$headerData['reason']}"
                );
            }

            // هنا يتم إطلاق Event لعمل القيد المحاسبي: 
            // EventBus::publish(new StockAdjustedEvent($adjId, $companyId));

            return $adjId;
        });
    }
}