<?php
// Path: app/Core/Approval/ApproverResolver.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Approver Resolver
 * محرك ذكي يحدد "من يجب أن يوافق؟" بناءً على إعدادات الـ Workflow والتفويضات.
 */
class ApproverResolver
{
    protected DatabaseManager $db;
    protected DelegationManager $delegationManager;

    public function __construct(DatabaseManager $db, DelegationManager $delegationManager)
    {
        $this->db = $db;
        $this->delegationManager = $delegationManager;
    }

    /**
     * تحديد الموظف أو المدير الفعلي المطلوب منه اتخاذ القرار لهذه الخطوة.
     *
     * @param int $stepId
     * @param int $companyId
     * @return int|null يُرجع User ID أو Null إذا كانت الخطوة غير صالحة.
     */
    public function resolveApproverForStep(int $stepId, int $companyId): ?int
    {
        $sql = "SELECT approver_id, role_id FROM approval_steps WHERE id = ? LIMIT 1";
        $step = $this->db->connection()->selectOne($sql, [$stepId]);

        if (!$step) {
            return null;
        }

        $targetUserId = null;

        // إذا كانت الموافقة موجهة لمستخدم محدد بالاسم
        if (!empty($step['approver_id'])) {
            $targetUserId = (int) $step['approver_id'];
        } 
        // إذا كانت الموافقة موجهة لدور (Role) مثل "المدير المالي"
        elseif (!empty($step['role_id'])) {
            $roleUserSql = "SELECT user_id FROM user_roles 
                            INNER JOIN users ON user_roles.user_id = users.id 
                            WHERE role_id = ? AND users.company_id = ? AND users.is_active = 1 LIMIT 1";
            $roleUser = $this->db->connection()->selectOne($roleUserSql, [$step['role_id'], $companyId]);
            
            if ($roleUser) {
                $targetUserId = (int) $roleUser['user_id'];
            }
        }

        if ($targetUserId !== null) {
            // فحص ما إذا كان هذا المستخدم في إجازة ومفوض شخصاً آخر!
            return $this->delegationManager->getActiveDelegate($targetUserId, $companyId);
        }

        return null;
    }
}