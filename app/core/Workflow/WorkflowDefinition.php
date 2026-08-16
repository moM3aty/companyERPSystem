<?php
// Path: app/Core/Workflow/WorkflowDefinition.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Models\Entity;

/**
 * Enterprise Workflow Definition Entity
 * يمثل التعريف الأساسي لسير العمل (مثال: "دورة طلب الشراء").
 */
class WorkflowDefinition extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'name' => 'string',
        'code' => 'string', // e.g., 'purchase_order_flow'
        'is_active' => 'boolean',
    ];
}