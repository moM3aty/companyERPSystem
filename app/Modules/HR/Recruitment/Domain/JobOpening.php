<?php
// Path: app/Modules/HR/Recruitment/Domain/JobOpening.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise HR Domain Entity: Job Opening
 * إعلان توظيف (شاغر وظيفي) تابع لإدارة التوظيف.
 */
class JobOpening extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'department_id'   => 'integer',
        'title'           => 'string',
        'description'     => 'string',
        'requirements'    => 'string',
        'positions_count' => 'integer',
        'status'          => 'string', // 'draft', 'published', 'closed', 'cancelled'
        'closing_date'    => 'string',
        'created_by'      => 'integer',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}