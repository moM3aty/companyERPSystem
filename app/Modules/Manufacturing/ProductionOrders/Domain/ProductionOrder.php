<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Domain/ProductionOrder.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Production Order
 * The operational document instructing the shop floor to produce a quantity of a finished good based on a BOM.
 */
class ProductionOrder extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'branch_id'         => 'integer',
        'bom_id'            => 'integer',
        'product_id'        => 'integer',
        'order_number'      => 'string',
        'planned_quantity'  => 'float',
        'produced_quantity' => 'float',
        'status'            => 'string', // 'draft', 'planned', 'in_progress', 'completed', 'cancelled'
        'start_date'        => 'string', // YYYY-MM-DD
        'end_date'          => 'string', // YYYY-MM-DD
        'created_by'        => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}