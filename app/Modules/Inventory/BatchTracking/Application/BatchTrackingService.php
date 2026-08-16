<?php
// Path: app/Modules/Inventory/BatchTracking/Application/BatchTrackingService.php

declare(strict_types=1);

namespace App\Modules\Inventory\BatchTracking\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Batch Tracking Service
 * يدير عمليات سحب البضائع التي تملك تاريخ صلاحية بنظام الـ FEFO (First Expired First Out).
 */
class BatchTrackingService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تخصيص الكميات من الـ Batches المتاحة آلياً بناءً على تاريخ الانتهاء (FEFO).
     *
     * @param int $productId
     * @param int $warehouseId
     * @param float $requiredQty
     * @param int $companyId
     * @return array مصفوفة تحتوي على الـ Batches التي سيتم السحب منها وكمية كل سحب
     * @throws BusinessException
     */
    public function allocateFefo(int $productId, int $warehouseId, float $requiredQty, int $companyId): array
    {
        // جلب جميع الـ Batches النشطة والتي لم تنتهِ صلاحيتها لهذا الصنف، مرتبة بأقرب تاريخ انتهاء
        $sql = "SELECT id, batch_number, current_quantity, expiry_date 
                FROM inventory_batches 
                WHERE product_id = ? AND company_id = ? AND current_quantity > 0 AND is_active = 1
                  AND (expiry_date >= ? OR expiry_date IS NULL)
                ORDER BY expiry_date ASC, id ASC FOR UPDATE"; // Row Locking لمنع السحب المزدوج

        $batches = $this->db->connection()->select($sql, [$productId, $companyId, date('Y-m-d')]);

        $allocated = [];
        $remainingToAllocate = $requiredQty;

        foreach ($batches as $batch) {
            if ($remainingToAllocate <= 0) {
                break;
            }

            $availableInBatch = (float) $batch['current_quantity'];
            $qtyToTake = min($availableInBatch, $remainingToAllocate);

            $allocated[] = [
                'batch_id'     => $batch['id'],
                'batch_number' => $batch['batch_number'],
                'quantity'     => $qtyToTake,
            ];

            $remainingToAllocate -= $qtyToTake;
        }

        if ($remainingToAllocate > 0) {
            throw new BusinessException("Not enough valid unexpired batch stock available for product ID [{$productId}]. Shortage: {$remainingToAllocate}", 422);
        }

        return $allocated;
    }

    /**
     * خصم الكميات المخصصة فعلياً من الـ Batches.
     *
     * @param array $allocations (المخرجات من الدالة allocateFefo)
     * @return void
     */
    public function consumeBatches(array $allocations): void
    {
        foreach ($allocations as $allocation) {
            $this->db->connection()->update(
                "UPDATE inventory_batches SET current_quantity = current_quantity - ? WHERE id = ?",
                [$allocation['quantity'], $allocation['batch_id']]
            );
        }
    }
}