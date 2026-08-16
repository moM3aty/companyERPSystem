<?php
// Path: app/Modules/Inventory/Models/ProductVariant.php

declare(strict_types=1);

namespace App\Modules\Inventory\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Model: Product Variant
 * يمثل تشكيلات المنتج الواحد (مثل: القمصان بمقاسات وألوان مختلفة).
 */
class ProductVariant extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'parent_product_id'=> 'integer',
        'sku'              => 'string', // كود مميز للنسخة
        'attributes'       => 'json',   // مثال: ['color' => 'Red', 'size' => 'XL']
        'price_modifier'   => 'float',  // فارق السعر عن المنتج الأصلي
        'is_active'        => 'boolean',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}