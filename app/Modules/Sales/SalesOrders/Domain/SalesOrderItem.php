<?php
// Path: app/Modules/Sales/SalesOrders/Domain/SalesOrderItem.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Domain;

use App\Core\Models\Entity;

class SalesOrderItem extends Entity
{
    protected array $casts = [
        'id'                 => 'integer',
        'sales_order_id'     => 'integer',
        'product_id'         => 'integer',
        'description'        => 'string',
        'quantity'           => 'float',
        'delivered_quantity' => 'float', // يتحدث آلياً عند خروج Delivery Note
        'invoiced_quantity'  => 'float', // يتحدث آلياً عند خروج Invoice
        'unit_price'         => 'float',
        'discount_amount'    => 'float',
        'tax_amount'         => 'float',
        'total'              => 'float',
    ];
}