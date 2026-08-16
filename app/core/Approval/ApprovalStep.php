<?php
// Path: app/Core/Approval/ApprovalStep.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Models\Entity;

/**
 * Enterprise Approval Step Entity
 * يمثل خطوة واحدة داخل سلسلة الموافقات (مثلاً: موافقة المدير المباشر، ثم المدير المالي).
 */
class ApprovalStep extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'approval_request_id' => 'integer',
        'approver_id' => 'integer', // المستخدم المطلوب منه الموافقة
        'role_id' => 'integer', // أو يمكن أن تكون الموافقة مبنية على الدور وليس مستخدم بعينه
        'level' => 'integer', // ترتيب الخطوة (1, 2, 3)
        'status' => 'string', // 'pending', 'approved', 'rejected', 'skipped'
        'is_current' => 'boolean', // هل هذه هي الخطوة النشطة حالياً؟
        'sla_hours' => 'integer', // الساعات المتاحة قبل تصعيد الطلب
    ];

    /**
     * التحقق مما إذا كانت الخطوة نشطة وتنتظر القرار.
     *
     * @return bool
     */
    public function isCurrentAndPending(): bool
    {
        return $this->getAttribute('is_current') === true && $this->getAttribute('status') === 'pending';
    }
}