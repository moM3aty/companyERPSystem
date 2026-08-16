<?php
// Path: app/Modules/Projects/Tasks/Domain/Task.php

declare(strict_types=1);

namespace App\Modules\Projects\Tasks\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Project Task
 * يمثل مهمة فرعية داخل المشروع، يتم تكليف موظف بها وتسجيل الساعات عليها.
 */
class Task extends Entity
{
    protected array $casts = [
        'id'              => 'integer',
        'project_id'      => 'integer',
        'name'            => 'string',
        'description'     => 'string',
        'assigned_to'     => 'integer', // User ID
        'status'          => 'string', // 'todo', 'in_progress', 'review', 'done'
        'priority'        => 'string', // 'low', 'normal', 'high', 'urgent'
        'estimated_hours' => 'float',
        'logged_hours'    => 'float',
        'start_date'      => 'string',
        'due_date'        => 'string',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}