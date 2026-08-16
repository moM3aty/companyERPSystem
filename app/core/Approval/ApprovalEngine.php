<?php
// Path: app/Core/Approval/ApprovalEngine.php

declare(strict_types=1);

namespace App\Core\Approval;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Events\EventBus; // Assuming an EventBus exists for triggering domain events

/**
 * Enterprise Approval Engine
 * العقل المدبر لنظام الموافقات. يتحكم في إنشاء الطلبات، وتسجيل قرارات المستخدمين،
 * وتقديم حالة المستند للأمام في الدورة المستندية.
 */
class ApprovalEngine
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected ApproverResolver $resolver;

    public function __construct(
        DatabaseManager $db, 
        TransactionManager $transaction,
        ApproverResolver $resolver
    ) {
        $this->db = $db;
        $this->transaction = $transaction;
        $this->resolver = $resolver;
    }

    /**
     * معالجة قرار صادر من مستخدم لطلب موافقة.
     *
     * @param ApprovalDecision $decision
     * @param int $companyId
     * @return void
     * @throws BusinessException|\Throwable
     */
    public function processDecision(ApprovalDecision $decision, int $companyId): void
    {
        $this->transaction->execute(function () use ($decision, $companyId) {
            
            // 1. جلب الطلب والتحقق من حالته
            $request = $this->db->connection()->selectOne(
                "SELECT * FROM approval_requests WHERE id = ? AND company_id = ? FOR UPDATE", 
                [$decision->requestId, $companyId]
            );

            if (!$request || $request['status'] !== 'pending') {
                throw new BusinessException("Approval request is either invalid or already processed.");
            }

            // 2. جلب الخطوة الحالية النشطة
            $currentStep = $this->db->connection()->selectOne(
                "SELECT * FROM approval_steps WHERE approval_request_id = ? AND is_current = 1 AND status = 'pending' FOR UPDATE",
                [$decision->requestId]
            );

            if (!$currentStep) {
                throw new BusinessException("No active pending step found for this request.");
            }

            // 3. التحقق الأمني: هل هذا المستخدم هو المخول بالموافقة؟ (يأخذ في الاعتبار التفويضات)
            $expectedApproverId = $this->resolver->resolveApproverForStep((int) $currentStep['id'], $companyId);
            
            if ($expectedApproverId !== $decision->approverId) {
                throw new BusinessException("Unauthorized: You are not the designated approver for this step.", 403);
            }

            $now = date('Y-m-d H:i:s');

            // 4. تسجيل التاريخ (Audit History)
            $this->db->connection()->insert(
                "INSERT INTO approval_histories (approval_request_id, step_id, approver_id, action, comments, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$decision->requestId, $currentStep['id'], $decision->approverId, $decision->action, $decision->comments, $now]
            );

            // 5. اتخاذ القرار
            if ($decision->action === 'reject') {
                // رفض: نقفل الخطوة والطلب بالكامل
                $this->db->connection()->update(
                    "UPDATE approval_steps SET status = 'rejected', is_current = 0, updated_at = ? WHERE id = ?",
                    [$now, $currentStep['id']]
                );
                
                $this->db->connection()->update(
                    "UPDATE approval_requests SET status = 'rejected', updated_at = ? WHERE id = ?",
                    [$now, $decision->requestId]
                );
                
                // EventBus::publish(new DocumentRejectedEvent(...));
                
            } else {
                // موافقة: نقفل الخطوة الحالية وننتقل للتالية
                $this->db->connection()->update(
                    "UPDATE approval_steps SET status = 'approved', is_current = 0, updated_at = ? WHERE id = ?",
                    [$now, $currentStep['id']]
                );

                $nextLevel = (int) $currentStep['level'] + 1;
                
                $affected = $this->db->connection()->update(
                    "UPDATE approval_steps SET is_current = 1, created_at = ?, updated_at = ? 
                     WHERE approval_request_id = ? AND level = ?",
                    [$now, $now, $decision->requestId, $nextLevel]
                );

                // إذا لم يكن هناك خطوات تالية، نعتمد الطلب بالكامل
                if ($affected === 0) {
                    $this->db->connection()->update(
                        "UPDATE approval_requests SET status = 'approved', updated_at = ? WHERE id = ?",
                        [$now, $decision->requestId]
                    );
                    
                    // EventBus::publish(new DocumentApprovedEvent(...));
                }
            }
        });
    }
}