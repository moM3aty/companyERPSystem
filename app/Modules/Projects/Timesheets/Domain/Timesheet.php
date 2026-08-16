<?php
// Path: app/Modules/Projects/Timesheets/Domain/Timesheet.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Timesheet
 * سجل الوقت للموظفين على المشاريع. يستخدم لتتبع التكاليف العمالية بدقة (Actual Labor Cost).
 */
class Timesheet extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'project_id'  => 'integer',
        'task_id'     => 'integer',
        'employee_id' => 'integer',
        'log_date'    => 'string', // YYYY-MM-DD
        'hours'       => 'float',
        'description' => 'string',
        'status'      => 'string', // 'draft', 'submitted', 'approved', 'rejected'
        'approved_by' => 'integer',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}