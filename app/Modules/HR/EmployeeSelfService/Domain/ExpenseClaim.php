<?php
// Path: app/Modules/HR/EmployeeSelfService/Domain/ExpenseClaim.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Expense Claim (ESS)
 * يمثل مطالبة الموظف باسترداد مصروفات (سفر، بنزين، عهدة).
 */
class ExpenseClaim extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'employee_id'  => 'integer',
        'claim_no'     => 'string',
        'claim_date'   => 'string',
        'total_amount' => 'float',
        'currency_id'  => 'integer',
        'purpose'      => 'string',
        'status'       => 'string', // 'pending', 'approved', 'rejected', 'paid'
        'payment_id'   => 'integer', // يربط بسند الصرف لاحقاً
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];
}