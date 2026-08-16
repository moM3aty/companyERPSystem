<?php
// Path: app/Core/Approval/EscalationManager.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Escalation Manager
 * خدمة تعمل في الخلفية (عبر Scheduler) للبحث عن طلبات الموافقة المتأخرة 
 * وتصعيدها تلقائياً للمدير الأعلى لمنع تعطل العمل في الشركة.
 */
class EscalationManager
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected LoggerInterface $logger;

    public function __construct(DatabaseManager $db, TransactionManager $transaction, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->logger = $logger;
    }

    /**
     * فحص وتصعيد الطلبات المتأخرة.
     *
     * @return int عدد الطلبات التي تم تصعيدها.
     */
    public function processEscalations(): int
    {
        $escalatedCount = 0;
        $now = date('Y-m-d H:i:s');

        // البحث عن الخطوات النشطة التي تجاوزت وقت المعالجة المسموح (SLA)
        $sql = "SELECT s.*, r.company_id 
                FROM approval_steps s
                JOIN approval_requests r ON s.approval_request_id = r.id
                WHERE s.is_current = 1 
                  AND s.status = 'pending' 
                  AND s.sla_hours > 0 
                  AND DATE_ADD(s.created_at, INTERVAL s.sla_hours HOUR) <= ?";

        $overdueSteps = $this->db->connection()->select($sql, [$now]);

        foreach ($overdueSteps as $step) {
            try {
                $this->escalateStep($step);
                $escalatedCount++;
            } catch (\Throwable $e) {
                $this->logger->error("Failed to escalate approval step ID [{$step['id']}]: " . $e->getMessage());
            }
        }

        return $escalatedCount;
    }

    /**
     * تنفيذ تصعيد خطوة معينة عبر تخطيها والانتقال للمستوى التالي.
     *
     * @param array $step
     * @return void
     * @throws \Throwable
     */
    protected function escalateStep(array $step): void
    {
        $this->transaction->execute(function () use ($step) {
            $now = date('Y-m-d H:i:s');
            
            // 1. تجاوز الخطوة الحالية
            $this->db->connection()->update(
                "UPDATE approval_steps SET status = 'skipped', is_current = 0, updated_at = ? WHERE id = ?",
                [$now, $step['id']]
            );

            // 2. تسجيل التصعيد في التاريخ
            $this->db->connection()->insert(
                "INSERT INTO approval_histories (approval_request_id, step_id, approver_id, action, comments, created_at) 
                 VALUES (?, ?, ?, 'escalated', 'System auto-escalation due to SLA breach', ?)",
                [$step['approval_request_id'], $step['id'], $step['approver_id'] ?? 0, $now]
            );

            // 3. تفعيل الخطوة التالية إن وجدت
            $nextLevel = (int) $step['level'] + 1;
            
            $affected = $this->db->connection()->update(
                "UPDATE approval_steps SET is_current = 1, created_at = ?, updated_at = ? 
                 WHERE approval_request_id = ? AND level = ?",
                [$now, $now, $step['approval_request_id'], $nextLevel]
            );

            // إذا لم يكن هناك خطوة تالية، نقوم بالموافقة على الطلب بالكامل
            if ($affected === 0) {
                $this->db->connection()->update(
                    "UPDATE approval_requests SET status = 'approved', updated_at = ? WHERE id = ?",
                    [$now, $step['approval_request_id']]
                );
                
                // في نظام حقيقي، سيتم هنا إطلاق حدث (Event) لإخبار النظام بأن المستند تم اعتماده
                // EventBus::publish(new DocumentApprovedEvent($step['document_type'], $step['document_id']));
            }
        });
    }
}