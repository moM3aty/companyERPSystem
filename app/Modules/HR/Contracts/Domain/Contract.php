<?php
// Path: app/Modules/HR/Contracts/Domain/Contract.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Employee Contract
 * يعبر عن العقد الوظيفي والمالي للموظف (مهم جداً للـ Payroll).
 */
class Contract extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'employee_id'    => 'integer',
        'contract_type'  => 'string', // full_time, part_time, freelance
        'start_date'     => 'string', // YYYY-MM-DD
        'end_date'       => 'string', // YYYY-MM-DD
        'basic_salary'   => 'float',
        'currency_id'    => 'integer',
        'working_hours'  => 'integer',
        'probation_days' => 'integer',
        'status'         => 'string', // draft, active, expired, terminated
        'created_at'     => 'string',
        'updated_at'     => 'string',
        'deleted_at'     => 'string',
    ];

    /**
     * التحقق مما إذا كان العقد سارياً بناءً على التاريخ.
     *
     * @return bool
     */
    public function isValidAndActive(): bool
    {
        if ($this->getAttribute('status') !== 'active') {
            return false;
        }

        $now = date('Y-m-d');
        $endDate = $this->getAttribute('end_date');

        return !$endDate || $endDate >= $now;
    }
}