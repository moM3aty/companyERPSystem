<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Domain/PurchaseOrderItem.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Purchase Order Item
 * سطور أمر الشراء.
 */
class PurchaseOrderItem extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'purchase_order_id' => 'integer',
        'product_id'        => 'integer',
        'description'       => 'string',
        'quantity'          => 'float',
        'unit_price'        => 'float',
        'discount_amount'   => 'float',
        'tax_amount'        => 'float',
        'total'             => 'float',
    ];
}