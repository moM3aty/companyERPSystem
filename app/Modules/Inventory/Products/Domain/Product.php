<?php
// Path: app/Modules/Inventory/Products/Domain/Product.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Product
 * الكيان الأساسي للأصناف والخدمات في النظام.
 */
class Product extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'category_id'          => 'integer',
        'brand_id'             => 'integer',
        'type'                 => 'string', // 'storable', 'service', 'consumable'
        'cost_method'          => 'string', // 'fifo', 'average', 'standard'
        'code'                 => 'string', // SKU
        'barcode'              => 'string',
        'name'                 => 'string',
        'description'          => 'string',
        'base_unit_id'         => 'integer',
        'default_tax_id'       => 'integer',
        'is_active'            => 'boolean',
        'track_batches'        => 'boolean',
        'track_serials'        => 'boolean',
        'created_at'           => 'string',
        'updated_at'           => 'string',
        'deleted_at'           => 'string',
    ];

    /**
     * التحقق مما إذا كان الصنف قابلاً للتخزين الفعلي (يُحسب له رصيد).
     *
     * @return bool
     */
    public function isStorable(): bool
    {
        return $this->getAttribute('type') === 'storable';
    }
}