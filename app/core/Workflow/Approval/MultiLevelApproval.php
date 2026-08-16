<?php
// Path: app/Core/Workflow/Approval/MultiLevelApproval.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Multi-Level Approval Orchestrator
 * يدير نقل الطلب تصاعدياً عبر المستويات (من Manager إلى Finance إلى CEO).
 */
class MultiLevelApproval
{
    protected DatabaseManager $db;
    protected ParallelApproval $parallelApproval;

    public function __construct(DatabaseManager $db, ParallelApproval $parallelApproval)
    {
        $this->db = $db;
        $this->parallelApproval = $parallelApproval;
    }

    /**
     * الانتقال للمستوى التالي من الموافقات.
     *
     * @param int $requestId
     * @param int $currentLevel
     * @return bool يُرجع True إذا تم اعتماد الطلب بالكامل لعدم وجود مستويات أخرى
     */
    public function advanceToNextLevel(int $requestId, int $currentLevel): bool
    {
        // إذا كان المستوى الحالي متوازياً (Parallel)، نتحقق من موافقة الجميع أولاً
        if (!$this->parallelApproval->isLevelFullyApproved($requestId, $currentLevel)) {
            return false; // ننتظر موافقة بقية الأطراف في نفس المستوى
        }

        $nextLevel = $currentLevel + 1;
        $now = date('Y-m-d H:i:s');

        // محاولة تفعيل الخطوة التالية
        $affectedRows = $this->db->connection()->update(
            "UPDATE approval_steps SET is_current = 1, status = 'pending', updated_at = ? 
             WHERE approval_request_id = ? AND level = ?",
            [$now, $requestId, $nextLevel]
        );

        // إذا لم يكن هناك أي صفوف تأثرت، إذن لا يوجد مستوى تالي = الطلب معتمد نهائياً!
        if ($affectedRows === 0) {
            $this->db->connection()->update(
                "UPDATE approval_requests SET status = 'approved', updated_at = ? WHERE id = ?",
                [$now, $requestId]
            );
            return true;
        }

        return false;
    }
}