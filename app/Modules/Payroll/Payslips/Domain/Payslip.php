<?php
// Path: app/Modules/Payroll/Payslips/Domain/Payslip.php

declare(strict_types=1);

namespace App\Modules\Payroll\Payslips\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Payslip
 * يمثل قسيمة الراتب الفردية لموظف واحد داخل مسير الرواتب.
 */
class Payslip extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'payroll_run_id'   => 'integer',
        'employee_id'      => 'integer',
        'contract_id'      => 'integer',
        'basic_salary'     => 'float',
        'allowances'       => 'float',
        'deductions'       => 'float',
        'net_salary'       => 'float',
        'details'          => 'json', // JSON document storing the exact breakdown of allowances/deductions for historical accuracy
    ];
}