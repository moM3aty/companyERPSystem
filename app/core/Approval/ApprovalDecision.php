<?php
// Path: app/Core/Approval/ApprovalDecision.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Approval Decision DTO
 * كائن يحمل قرار المستخدم (موافقة أو رفض) والتعليقات المرفقة.
 * استخدامه كـ Object يمنع تمرير بيانات خاطئة للمحرك.
 */
class ApprovalDecision
{
    public readonly int $requestId;
    public readonly int $approverId;
    public readonly string $action;
    public readonly string $comments;

    /**
     * ApprovalDecision constructor.
     *
     * @param int $requestId
     * @param int $approverId
     * @param string $action ('approve' or 'reject')
     * @param string $comments
     * @throws BusinessException
     */
    public function __construct(int $requestId, int $approverId, string $action, string $comments = '')
    {
        $action = strtolower(trim($action));
        
        if (!in_array($action, ['approve', 'reject'], true)) {
            throw new BusinessException("Invalid approval action: [{$action}]. Must be 'approve' or 'reject'.");
        }

        if ($action === 'reject' && empty(trim($comments))) {
            throw new BusinessException("Comments are mandatory when rejecting an approval request.");
        }

        $this->requestId = $requestId;
        $this->approverId = $approverId;
        $this->action = $action;
        $this->comments = trim($comments);
    }
}