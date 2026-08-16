<?php
// Path: app/Modules/Inventory/Models/InventoryTransaction.php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Inventory Transaction
 * يمثل كارتة الصنف (Item Ledger) غير القابلة للتعديل.
 */
class InventoryTransaction extends Entity
{
    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'product_id'     => 'integer',
        'warehouse_id'   => 'integer',
        'type'           => 'string', // 'IN', 'OUT'
        'quantity'       => 'float',
        'unit_cost'      => 'float',
        'total_value'    => 'float',
        'reference_type' => 'string', // 'sales_invoice', 'goods_receipt', 'adjustment'
        'reference_id'   => 'integer',
        'created_at'     => 'string',
    ];
}