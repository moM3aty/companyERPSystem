<?php
// Path: app/Modules/CRM/Customers/Domain/Customer.php

declare(strict_types=1);

namespace App\Modules\CRM\Customers\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Customer
 * الكيان الأساسي للعميل في نظام إدارة علاقات العملاء (CRM) والمبيعات.
 */
class Customer extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'customer_code'     => 'string',
        'name'              => 'string',
        'email'             => 'string',
        'phone'             => 'string',
        'tax_number'        => 'string',
        'credit_limit'      => 'float',
        'payment_term_id'   => 'integer',
        'is_active'         => 'boolean',
        'created_at'        => 'string',
        'updated_at'        => 'string',
        'deleted_at'        => 'string',
    ];
}