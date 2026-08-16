<?php
// Path: app/Modules/Inventory/Warehouses/Domain/Warehouse.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Warehouse
 * يمثل المستودع الفعلي الذي يتم فيه تخزين الأصناف واستلامها وصرفها.
 */
class Warehouse extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'location_id'      => 'integer',
        'code'             => 'string',
        'name'             => 'string',
        'address'          => 'string',
        'is_active'        => 'boolean',
        'is_transit'       => 'boolean', // مستودع وسيط لنقل البضائع
        'created_at'       => 'string',
        'updated_at'       => 'string',
        'deleted_at'       => 'string',
    ];
}