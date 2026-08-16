<?php
// Path: app/Modules/Inventory/StockMovements/Domain/Events/StockUpdatedEvent.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Stock Updated
 * يُطلق بعد تسجيل حركة المخزون لتنبيه النظام (مثل إبلاغ قسم المشتريات باقتراب نفاذ الكمية).
 */
class StockUpdatedEvent extends DomainEvent
{
    public readonly int $productId;
    public readonly int $warehouseId;
    public readonly float $quantityChanged;
    public readonly float $newBalance;
    public readonly string $movementType;
    public readonly int $companyId;

    public function __construct(
        int $productId,
        int $warehouseId,
        float $quantityChanged,
        float $newBalance,
        string $movementType,
        int $companyId
    ) {
        parent::__construct($productId);
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->quantityChanged = $quantityChanged;
        $this->newBalance = $newBalance;
        $this->movementType = $movementType;
        $this->companyId = $companyId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'warehouse_id'     => $this->warehouseId,
            'quantity_changed' => $this->quantityChanged,
            'new_balance'      => $this->newBalance,
            'movement_type'    => $this->movementType,
            'company_id'       => $this->companyId,
        ]);
    }
}