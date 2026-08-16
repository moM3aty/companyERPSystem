<?php
// Path: app/Modules/HR/Positions/Domain/Position.php

declare(strict_types=1);

namespace App\Modules\HR\Positions\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise HR Domain Entity: Job Position
 * يمثل المسمى الوظيفي المعتمد في الشركة (مثال: Senior Software Engineer).
 */
class Position extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'            => 'integer',
        'company_id'    => 'integer',
        'department_id' => 'integer',
        'title'         => 'string',
        'job_code'      => 'string',
        'description'   => 'string',
        'is_active'     => 'boolean',
        'created_at'    => 'string',
        'updated_at'    => 'string',
    ];
}