<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Domain/Events/ProductionOrderCompletedEvent.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Production Order Completed
 * Fired when manufacturing is finished. 
 * Essential for the Inventory Module to listen to this event so it can automatically:
 * 1. Decrease stock of raw materials (consumption).
 * 2. Increase stock of the finished good.
 */
class ProductionOrderCompletedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $productId; // Finished Good
    public readonly float $producedQuantity;
    public readonly int $orderId;

    public function __construct(int $orderId, int $companyId, int $productId, float $producedQuantity)
    {
        parent::__construct($orderId);
        $this->orderId = $orderId;
        $this->companyId = $companyId;
        $this->productId = $productId;
        $this->producedQuantity = $producedQuantity;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'        => $this->companyId,
            'order_id'          => $this->orderId,
            'product_id'        => $this->productId,
            'produced_quantity' => $this->producedQuantity,
        ]);
    }
}