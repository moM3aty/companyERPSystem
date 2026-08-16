<?php
// Path: app/Modules/HR/Training/Domain/TrainingProgram.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Training Program
 * يمثل دورة تدريبية مقدمة لموظفي الشركة لتطوير كفاءاتهم (Competencies).
 */
class TrainingProgram extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'title'            => 'string',
        'description'      => 'string',
        'instructor_name'  => 'string',
        'start_date'       => 'string', // YYYY-MM-DD
        'end_date'         => 'string', // YYYY-MM-DD
        'max_participants' => 'integer',
        'budget'           => 'float',  // تكلفة الدورة التدريبية على الشركة
        'status'           => 'string', // 'planned', 'active', 'completed', 'cancelled'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}