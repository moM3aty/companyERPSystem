<?php
// Path: app/Core/Workflow/WorkflowVersion.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Models\Entity;

/**
 * Enterprise Workflow Version Entity
 * إدارة الإصدارات تضمن أن التعديلات الجديدة على سير العمل لا تكسر الطلبات القديمة 
 * التي بدأت بالفعل باستخدام إصدار سابق.
 */
class WorkflowVersion extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'workflow_definition_id' => 'integer',
        'version_number' => 'integer',
        'status' => 'string', // 'draft', 'published', 'archived'
        'published_at' => 'string',
    ];
}