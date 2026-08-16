<?php
// Path: app/Core/Workflow/WorkflowStep.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Models\Entity;

/**
 * Enterprise Workflow Step Entity
 * يمثل خطوة أو حالة واحدة داخل الإصدار (مثال: "بانتظار المراجعة المالية").
 */
class WorkflowStep extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'workflow_version_id' => 'integer',
        'name' => 'string',
        'type' => 'string', // 'start', 'process', 'approval', 'end'
        'is_start_step' => 'boolean',
    ];
}