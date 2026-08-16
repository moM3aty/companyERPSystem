<?php
// Path: app/Core/Workflow/WorkflowInstance.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Models\Entity;

/**
 * Enterprise Workflow Instance Entity
 * يمثل تشغيل فعلي لسير العمل على مستند محدد (مثال: سير العمل لطلب الشراء رقم 1005).
 */
class WorkflowInstance extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'workflow_version_id' => 'integer',
        'entity_type' => 'string',
        'entity_id' => 'integer',
        'current_step_id' => 'integer',
        'status' => 'string', // 'active', 'completed', 'cancelled', 'failed'
        'payload' => 'json', // يحفظ حالة البيانات وقت التشغيل
    ];
}