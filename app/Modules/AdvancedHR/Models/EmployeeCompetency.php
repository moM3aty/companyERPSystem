<?php
// Path: app/Modules/AdvancedHR/Models/EmployeeCompetency.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Employee Competency Matrix
 * يمثل مستوى إجادة موظف معين لمهارة محددة (يستخدم في تقييم الترقيات).
 */
class EmployeeCompetency extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'employee_id'       => 'integer',
        'competency_id'     => 'integer',
        'proficiency_level' => 'integer', // 1 (Beginner) to 5 (Expert)
        'assessor_id'       => 'integer', // من قام بتقييمه؟
        'last_assessed_at'  => 'string',
    ];
}