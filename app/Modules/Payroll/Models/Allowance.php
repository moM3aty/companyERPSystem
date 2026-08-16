<?php
// Path: app/Modules/Payroll/Models/Allowance.php

declare(strict_types=1);

namespace App\Modules\Payroll\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Allowance
 * يمثل البدلات المالية (سكن، انتقال، طعام) وهل تخضع لضريبة كسب العمل أم لا.
 */
class Allowance extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'code'           => 'string', // e.g., 'ALW-HOU'
        'name'           => 'string', // e.g., 'Housing Allowance'
        'default_amount' => 'float',
        'is_taxable'     => 'boolean', // هل يخضع لاقتطاع الضرائب؟
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}