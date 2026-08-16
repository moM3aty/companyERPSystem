<?php
// Path: app/Modules/HR/Attendance/Domain/AttendanceRecord.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Attendance Record
 * يمثل بصمة الموظف اليومية (حضور وانصراف).
 */
class AttendanceRecord extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'employee_id'    => 'integer',
        'record_date'    => 'string', // YYYY-MM-DD
        'check_in_time'  => 'string', // HH:MM:SS
        'check_out_time' => 'string', // HH:MM:SS
        'status'         => 'string', // 'present', 'absent', 'late', 'on_leave'
        'late_minutes'   => 'integer',
        'notes'          => 'string',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}