<?php
// Path: app/Core/Workflow/Approval/ParallelApproval.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Parallel Approval
 * يحل مشكلة الإجراءات المتوازية (مثال: طلب تعيين موظف يحتاج موافقة مدير IT "و" مدير HR في نفس الوقت).
 */
class ParallelApproval
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل موافقة موازية والتحقق مما إذا كان كل الأطراف قد وافقوا ليتم نقل المستند للمستوى التالي.
     *
     * @param int $requestId
     * @param int $level المستوى الحالي
     * @return bool يُرجع True إذا اكتملت جميع الموافقات في هذا المستوى
     */
    public function isLevelFullyApproved(int $requestId, int $level): bool
    {
        $sql = "SELECT COUNT(*) as pending_count FROM approval_steps 
                WHERE approval_request_id = ? AND level = ? AND status = 'pending'";

        $result = $this->db->connection()->selectOne($sql, [$requestId, $level]);

        // إذا كان عدد الموافقات المعلقة صفراً، فهذا يعني أن الجميع وافق
        return ((int) $result['pending_count']) === 0;
    }
}