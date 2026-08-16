<?php
// Path: app/Core/Approval/ApprovalHistory.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Models\Entity;

/**
 * Enterprise Approval History Entity
 * سجل غير قابل للتعديل (Immutable) يحفظ تاريخ القرارات والتعليقات لغرض التدقيق (Audit).
 */
class ApprovalHistory extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'approval_request_id' => 'integer',
        'step_id' => 'integer',
        'approver_id' => 'integer',
        'action' => 'string', // 'approved', 'rejected', 'delegated', 'escalated'
        'comments' => 'string',
    ];
}