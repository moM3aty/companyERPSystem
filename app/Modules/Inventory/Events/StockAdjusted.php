<?php
// Path: app/Modules/Inventory/Events/StockAdjusted.php

declare(strict_types=1);

namespace App\Modules\Inventory\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Event: Stock Adjusted
 * يُطلق عند تنفيذ أي تسوية جردية لتحديث التقارير المالية والإحصائيات اللحظية.
 */
class StockAdjusted extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $warehouseId;
    public readonly float $totalDifferenceValue; // قيمة العجز أو الزيادة

    public function __construct(int $adjustmentId, int $companyId, int $warehouseId, float $totalDifferenceValue)
    {
        parent::__construct($adjustmentId);
        $this->companyId = $companyId;
        $this->warehouseId = $warehouseId;
        $this->totalDifferenceValue = $totalDifferenceValue;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'             => $this->companyId,
            'warehouse_id'           => $this->warehouseId,
            'total_difference_value' => $this->totalDifferenceValue,
        ]);
    }
}