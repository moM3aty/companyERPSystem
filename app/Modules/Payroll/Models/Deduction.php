<?php
// Path: app/Modules/Payroll/Models/Deduction.php

declare(strict_types=1);

namespace App\Modules\Payroll\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Deduction
 * يمثل الاستقطاعات المالية (جزاءات، تأخير، تأمين صحي).
 */
class Deduction extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'code'           => 'string', // e.g., 'DED-MED'
        'name'           => 'string', // e.g., 'Medical Insurance'
        'default_amount' => 'float',
        'is_pre_tax'     => 'boolean', // هل يتم خصمه قبل أم بعد حساب الضريبة؟
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}