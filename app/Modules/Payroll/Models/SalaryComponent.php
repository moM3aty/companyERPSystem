<?php
// Path: app/Modules/Payroll/Models/SalaryComponent.php
declare(strict_types=1);

namespace App\Modules\Payroll\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise Domain Entity: Salary Component
 * يمثل مكونات الراتب الثابتة (مثل: بدل السكن، بدل النقل) المرتبطة بهيكل الراتب.
 */
class SalaryComponent extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'            => 'integer',
        'company_id'    => 'integer',
        'name'          => 'string', // e.g., 'Housing Allowance'
        'type'          => 'string', // 'allowance', 'deduction'
        'amount_type'   => 'string', // 'fixed', 'percentage'
        'value'         => 'float',  // القيمة الثابتة أو النسبة المئوية
        'is_taxable'    => 'boolean',
        'is_active'     => 'boolean',
    ];
}