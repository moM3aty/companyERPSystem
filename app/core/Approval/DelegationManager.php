<?php
// Path: app/Core/Approval/DelegationManager.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Delegation Manager
 * يتحقق مما إذا كان المدير قد فوض صلاحياته لموظف آخر (بسبب إجازة أو سفر) 
 * ليتم توجيه طلبات الموافقة للموظف البديل تلقائياً.
 */
class DelegationManager
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * جلب المعرف الفعلي (ID) للمستخدم الذي سيقوم بالموافقة.
     * إذا كان المستخدم الأصلي مفوضاً لصلاحياته، سيتم إرجاع معرف المفوض إليه.
     *
     * @param int $originalApproverId
     * @param int $companyId
     * @return int
     */
    public function getActiveDelegate(int $originalApproverId, int $companyId): int
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT delegate_user_id 
                FROM approval_delegations 
                WHERE delegator_user_id = ? 
                  AND company_id = ? 
                  AND is_active = 1 
                  AND start_date <= ? 
                  AND end_date >= ?
                ORDER BY id DESC LIMIT 1";

        $delegation = $this->db->connection()->selectOne($sql, [
            $originalApproverId,
            $companyId,
            $now,
            $now
        ]);

        if ($delegation && isset($delegation['delegate_user_id'])) {
            return (int) $delegation['delegate_user_id'];
        }

        // لا يوجد تفويض نشط، نرجع المستخدم الأصلي
        return $originalApproverId;
    }
}