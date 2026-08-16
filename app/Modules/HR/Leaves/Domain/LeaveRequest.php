<?php
// Path: app/Modules/HR/Leaves/Domain/LeaveRequest.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Leave Request
 * يمثل طلب إجازة لموظف.
 */
class LeaveRequest extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'employee_id' => 'integer',
        'leave_type'  => 'string', // annual, sick, unpaid, maternity
        'start_date'  => 'string', // YYYY-MM-DD
        'end_date'    => 'string', // YYYY-MM-DD
        'total_days'  => 'integer',
        'status'      => 'string', // pending, approved, rejected, cancelled
        'reason'      => 'string',
        'approved_by' => 'integer',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}