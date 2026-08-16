<?php
// Path: app/Modules/Inventory/Categories/Domain/ProductCategory.php

declare(strict_types=1);

namespace App\Modules\Inventory\Categories\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Product Category
 * يمثل شجرة التصنيفات المتداخلة للأصناف (مثال: إلكترونيات -> هواتف -> أبل).
 */
class ProductCategory extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'parent_id'   => 'integer',
        'code'        => 'string',
        'name'        => 'string',
        'description' => 'string',
        'level'       => 'integer', // عمق التصنيف في الشجرة
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}