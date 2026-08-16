<?php
// Path: app/Core/Workflow/Approval/DelegationService.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Delegation Service
 * يسمح للمديرين بتفويض صلاحيات الموافقة الخاص بهم (Delegation of Authority) لشخص آخر أثناء غيابهم.
 */
class DelegationService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * إنشاء تفويض جديد.
     *
     * @param int $delegatorId المدير صاحب الصلاحية
     * @param int $delegateId الموظف المفوض إليه
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param int $companyId
     * @return void
     * @throws BusinessException
     */
    public function createDelegation(int $delegatorId, int $delegateId, string $startDate, string $endDate, int $companyId): void
    {
        if ($delegatorId === $delegateId) {
            throw new BusinessException("You cannot delegate authority to yourself.");
        }

        if ($startDate > $endDate) {
            throw new BusinessException("Start date cannot be after end date.");
        }

        $this->db->connection()->insert(
            "INSERT INTO approval_delegations (company_id, delegator_user_id, delegate_user_id, start_date, end_date, is_active, created_at) 
             VALUES (?, ?, ?, ?, ?, 1, ?)",
            [$companyId, $delegatorId, $delegateId, $startDate, $endDate, date('Y-m-d H:i:s')]
        );
    }

    /**
     * البحث عن المفوّض النشط حالياً لمدير معين.
     *
     * @param int $approverId
     * @param int $companyId
     * @return int يُرجع معرف المفوض إليه، أو نفس المدير إذا لم يكن هناك تفويض.
     */
    public function resolveDelegate(int $approverId, int $companyId): int
    {
        $now = date('Y-m-d H:i:s');

        $sql = "SELECT delegate_user_id FROM approval_delegations 
                WHERE delegator_user_id = ? AND company_id = ? AND is_active = 1 
                AND start_date <= ? AND end_date >= ?
                ORDER BY id DESC LIMIT 1";

        $row = $this->db->connection()->selectOne($sql, [$approverId, $companyId, $now, $now]);

        return $row ? (int) $row['delegate_user_id'] : $approverId;
    }
}