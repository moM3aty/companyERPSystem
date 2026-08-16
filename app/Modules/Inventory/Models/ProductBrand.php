<?php
// Path: app/Modules/Inventory/Models/ProductBrand.php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;

/**
 * Enterprise Model: Product Brand
 * العلامة التجارية للصنف، ضرورية لفلترة التقارير (Reporting).
 */
class ProductBrand extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
        'deleted_at'  => 'string',
    ];
}