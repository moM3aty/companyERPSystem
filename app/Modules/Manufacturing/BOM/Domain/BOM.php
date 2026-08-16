<?php
// Path: app/Modules/Manufacturing/BOM/Domain/BOM.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Bill of Materials (BOM)
 * Represents the recipe or component list required to manufacture a finished product.
 */
class BOM extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'product_id'     => 'integer', // The finished good being produced
        'code'           => 'string',  // BOM Reference Code (e.g., BOM-FG-001)
        'name'           => 'string',
        'batch_quantity' => 'float',   // The standard lot size this BOM produces
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}