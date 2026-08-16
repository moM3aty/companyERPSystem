<?php
// Path: app/Modules/Payroll/Models/SalaryStructure.php

declare(strict_types=1);

namespace App\Modules\Payroll\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Salary Structure
 * يمثل هيكل الرواتب (مثال: الدرجة الأولى، الدرجة الثانية) الذي يتم ربطه بعقد الموظف.
 */
class SalaryStructure extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'name'        => 'string', // e.g., 'Senior Management Grade A'
        'base_salary' => 'float',  // الراتب الأساسي الافتراضي لهذا الهيكل
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}