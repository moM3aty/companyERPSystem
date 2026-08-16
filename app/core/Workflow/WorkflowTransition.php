<?php
// Path: app/Core/Workflow/WorkflowTransition.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Models\Entity;

/**
 * Enterprise Workflow Transition Entity
 * يمثل المسار الذي يربط بين خطوتين (من خطوة A إلى خطوة B).
 */
class WorkflowTransition extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'workflow_version_id' => 'integer',
        'from_step_id' => 'integer',
        'to_step_id' => 'integer',
        'name' => 'string', // 'Approve', 'Reject', 'Auto-Forward'
    ];
}