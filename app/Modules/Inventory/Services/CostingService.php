<?php
// Path: app/Modules/Inventory/Services/CostingService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Inventory Costing
 * مسؤول عن الحسابات الرياضية الدقيقة لتكلفة الأصناف المتبقية بناءً على (Moving Average Cost).
 */
class CostingService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * إعادة حساب متوسط التكلفة (Moving Average) لصنف بعد استلام بضاعة جديدة.
     * المعادلة: ((الرصيد القديم × التكلفة القديمة) + (الرصيد الوارد × التكلفة الجديدة)) / إجمالي الرصيد الجديد
     *
     * @param int $productId
     * @param float $oldQuantity
     * @param float $oldAvgCost
     * @param float $incomingQuantity
     * @param float $incomingUnitCost
     * @return float
     */
    public function calculateMovingAverage(int $productId, float $oldQuantity, float $oldAvgCost, float $incomingQuantity, float $incomingUnitCost): float
    {
        $totalNewQuantity = $oldQuantity + $incomingQuantity;
        
        if ($totalNewQuantity <= 0.0) {
            return $oldAvgCost; // منع القسمة على صفر
        }

        $oldValue = $oldQuantity * $oldAvgCost;
        $incomingValue = $incomingQuantity * $incomingUnitCost;
        
        $newAverageCost = ($oldValue + $incomingValue) / $totalNewQuantity;

        return round($newAverageCost, 4); // دقة 4 خانات عشرية قياسية في الـ ERP
    }
}