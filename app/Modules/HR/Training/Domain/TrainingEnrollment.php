<?php
// Path: app/Modules/HR/Training/Domain/TrainingEnrollment.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Training Enrollment
 * تسجيل الموظف في دورة تدريبية وتقييمه بعدها.
 */
class TrainingEnrollment extends Entity
{
    protected array $casts = [
        'id'                  => 'integer',
        'training_program_id' => 'integer',
        'employee_id'         => 'integer',
        'status'              => 'string', // 'enrolled', 'completed', 'failed', 'dropped'
        'score'               => 'float',  // التقييم أو درجة الاختبار النهائي
        'feedback'            => 'string',
        'enrolled_at'         => 'string',
    ];
}