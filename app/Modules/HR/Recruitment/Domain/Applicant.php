<?php
// Path: app/Modules/HR/Recruitment/Domain/Applicant.php

declare(strict_types=1);

namespace App\Modules\HR\Recruitment\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise HR Domain Entity: Job Applicant
 * يمثل المتقدم لشاغر وظيفي.
 */
class Applicant extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'job_opening_id' => 'integer',
        'first_name'     => 'string',
        'last_name'      => 'string',
        'email'          => 'string',
        'phone'          => 'string',
        'resume_path'    => 'string',
        'status'         => 'string', // 'new', 'screening', 'interview', 'offered', 'hired', 'rejected'
        'rating'         => 'integer', // 1 to 5
        'applied_at'     => 'string',
        'updated_at'     => 'string',
    ];
}