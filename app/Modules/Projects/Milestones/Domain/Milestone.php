<?php
// Path: app/Modules/Projects/Milestones/Domain/Milestone.php

declare(strict_types=1);

namespace App\Modules\Projects\Milestones\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Project Milestone
 * يمثل نقطة مفصلية (مرحلة رئيسية) في المشروع. يُستخدم في الفوترة وإدارة الإنجاز.
 */
class Milestone extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'project_id'  => 'integer',
        'name'        => 'string',
        'description' => 'string',
        'due_date'    => 'string', // YYYY-MM-DD
        'status'      => 'string', // 'pending', 'achieved', 'delayed'
        'created_by'  => 'integer',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}