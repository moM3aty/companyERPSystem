<?php
// Path: app/Modules/Inventory/Models/WarehouseLocation.php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Warehouse Location (Bin / Aisle / Shelf)
 * يمثل التقسيم الداخلي للمستودع (ممرات، أرفف، صناديق) لنظام WMS متقدم.
 */
class WarehouseLocation extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'warehouse_id' => 'integer',
        'code'         => 'string', // e.g., 'A1-S2-B3' (Aisle 1, Shelf 2, Bin 3)
        'name'         => 'string',
        'barcode'      => 'string', // للتعرف عليه بمسدس الباركود
        'capacity'     => 'float',  // السعة التخزينية للحيز
        'is_active'    => 'boolean',
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];
}