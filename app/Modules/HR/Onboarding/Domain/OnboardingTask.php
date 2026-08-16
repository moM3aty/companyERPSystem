<?php
// Path: app/Modules/HR/Onboarding/Domain/OnboardingTask.php

declare(strict_types=1);

namespace App\Modules\HR\Onboarding\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Onboarding Task
 * قائمة المهام التي يجب إنجازها عند تعيين موظف جديد (مثال: إنشاء إيميل، تسليم لابتوب).
 */
class OnboardingTask extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'employee_id' => 'integer',
        'task_name'   => 'string',
        'assigned_to' => 'integer', // User ID (e.g., IT Admin, HR Officer)
        'status'      => 'string', // 'pending', 'completed'
        'completed_at'=> 'string',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}