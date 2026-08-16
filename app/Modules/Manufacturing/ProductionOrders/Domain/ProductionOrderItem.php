<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Domain/ProductionOrderItem.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Production Order Item
 * The calculated raw materials required to fulfill the Production Order.
 */
class ProductionOrderItem extends Entity
{
    protected array $casts = [
        'id'                   => 'integer',
        'production_order_id'  => 'integer',
        'component_product_id' => 'integer',
        'required_quantity'    => 'float', // Calculated from BOM (including scrap factor)
        'consumed_quantity'    => 'float', // Actual quantity issued from inventory
    ];
}