<?php
// Path: app/Core/Workflow/Approval/EscalationService.php

declare(strict_types=1);

namespace App\Core\Workflow\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Escalation Service
 * يقوم بالتصعيد الآلي لتخطي المديرين المتأخرين في الرد للحفاظ على استمرارية العمل (SLA Breach).
 */
class EscalationService
{
    protected DatabaseManager $db;
    protected LoggerInterface $logger;
    protected MultiLevelApproval $multiLevelApproval;

    public function __construct(DatabaseManager $db, LoggerInterface $logger, MultiLevelApproval $multiLevelApproval)
    {
        $this->db = $db;
        $this->logger = $logger;
        $this->multiLevelApproval = $multiLevelApproval;
    }

    /**
     * فحص كافة الطلبات المعلقة وتصعيد المتأخر منها (تُستدعى عبر الـ Scheduler يومياً).
     *
     * @return int عدد الطلبات المصعدة
     */
    public function processOverdueApprovals(): int
    {
        $now = date('Y-m-d H:i:s');
        $escalatedCount = 0;

        $sql = "SELECT id, approval_request_id, level, approver_id FROM approval_steps 
                WHERE is_current = 1 AND status = 'pending' 
                AND sla_deadline_at IS NOT NULL AND sla_deadline_at <= ?";

        $overdueSteps = $this->db->connection()->select($sql, [$now]);

        foreach ($overdueSteps as $step) {
            try {
                // 1. تجاوز الخطوة
                $this->db->connection()->update(
                    "UPDATE approval_steps SET status = 'escalated', is_current = 0, updated_at = ? WHERE id = ?",
                    [$now, $step['id']]
                );

                // 2. تسجيل تاريخ التصعيد
                $this->db->connection()->insert(
                    "INSERT INTO approval_histories (approval_request_id, step_id, approver_id, action, comments, created_at) 
                     VALUES (?, ?, ?, 'escalated', 'System Auto-Escalation due to SLA Timeout', ?)",
                    [$step['approval_request_id'], $step['id'], $step['approver_id'], $now]
                );

                // 3. التقدم للمستوى التالي
                $this->multiLevelApproval->advanceToNextLevel((int) $step['approval_request_id'], (int) $step['level']);
                
                $escalatedCount++;
                $this->logger->info("Escalated approval step [{$step['id']}] for request [{$step['approval_request_id']}].");
            } catch (\Throwable $e) {
                $this->logger->error("Failed to escalate step [{$step['id']}]: " . $e->getMessage());
            }
        }

        return $escalatedCount;
    }
}