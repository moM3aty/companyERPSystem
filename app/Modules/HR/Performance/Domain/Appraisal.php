<?php
// Path: app/Modules/HR/Performance/Domain/Appraisal.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Performance Appraisal
 * تقييم الأداء الدوري للموظف.
 */
class Appraisal extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'employee_id'      => 'integer',
        'evaluator_id'     => 'integer', // المدير الذي قام بالتقييم
        'period_start'     => 'string',  // YYYY-MM-DD
        'period_end'       => 'string',  // YYYY-MM-DD
        'overall_score'    => 'float',   // من 0 إلى 100
        'feedback'         => 'string',
        'goals_achieved'   => 'json',    // الأهداف التي تم تحقيقها
        'status'           => 'string',  // 'draft', 'submitted', 'acknowledged'
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}