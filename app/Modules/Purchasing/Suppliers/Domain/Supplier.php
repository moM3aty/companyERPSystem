<?php
// Path: app/Modules/Purchasing/Suppliers/Domain/Supplier.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Supplier (Vendor)
 * الكيان الأساسي للموردين في النظام.
 */
class Supplier extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'supplier_code'   => 'string',
        'name'            => 'string',
        'email'           => 'string',
        'phone'           => 'string',
        'tax_number'      => 'string',
        'credit_limit'    => 'float',
        'payment_term_id' => 'integer',
        'is_active'       => 'boolean',
        'created_at'      => 'string',
        'updated_at'      => 'string',
        'deleted_at'      => 'string',
    ];
}