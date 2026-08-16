<?php
// Path: app/Core/Workflow/WorkflowEngine.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Workflow Engine (The Orchestrator)
 * العقل المدبر. يدير بدء العمليات، تقييم الشروط، التحرك بين الخطوات، وتنفيذ الأكشنز.
 */
class WorkflowEngine
{
    protected WorkflowRepository $repository;
    protected WorkflowCondition $conditionEvaluator;
    protected WorkflowAction $actionExecutor;
    protected DatabaseManager $db;
    protected TransactionManager $transaction;
    protected LoggerInterface $logger;

    public function __construct(
        WorkflowRepository $repository,
        WorkflowCondition $conditionEvaluator,
        WorkflowAction $actionExecutor,
        DatabaseManager $db,
        TransactionManager $transaction,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->conditionEvaluator = $conditionEvaluator;
        $this->actionExecutor = $actionExecutor;
        $this->db = $db;
        $this->transaction = $transaction;
        $this->logger = $logger;
    }

    /**
     * بدء دورة سير عمل جديدة لمستند.
     *
     * @param string $workflowCode
     * @param int $entityId
     * @param string $entityType
     * @param int $companyId
     * @param array $initialPayload
     * @return WorkflowInstance
     * @throws BusinessException|\Throwable
     */
    public function start(string $workflowCode, int $entityId, string $entityType, int $companyId, array $initialPayload = []): WorkflowInstance
    {
        $version = $this->repository->getActiveVersion($workflowCode, $companyId);

        if (!$version) {
            throw new BusinessException("No active workflow version found for code: {$workflowCode}");
        }

        $startStep = $this->repository->getStartStep((int) $version->id);

        if (!$startStep) {
            throw new BusinessException("Workflow version [{$version->id}] is misconfigured. Missing a start step.");
        }

        return $this->transaction->execute(function () use ($version, $startStep, $entityId, $entityType, $initialPayload) {
            
            // 1. إنشاء الـ Instance
            $payloadJson = json_encode($initialPayload, JSON_UNESCAPED_UNICODE);
            $now = date('Y-m-d H:i:s');
            
            $this->db->connection()->insert(
                "INSERT INTO workflow_instances (workflow_version_id, entity_type, entity_id, current_step_id, status, payload, created_at, updated_at) 
                 VALUES (?, ?, ?, ?, 'active', ?, ?, ?)",
                [$version->id, $entityType, $entityId, $startStep->id, $payloadJson, $now, $now]
            );

            $instanceId = (int) $this->db->connection()->lastInsertId();

            $instanceData = $this->db->connection()->selectOne("SELECT * FROM workflow_instances WHERE id = ?", [$instanceId]);
            $instance = new WorkflowInstance($instanceData);

            // 2. تنفيذ أكشنز "الدخول" للخطوة الابتدائية
            $context = new WorkflowContext($instanceId, $entityId, $entityType, $initialPayload);
            $this->executeActions((int) $startStep->id, 'on_enter', $context);

            $this->logger->info("Started Workflow [{$workflowCode}] for Entity [{$entityType}:{$entityId}]");

            // 3. محاولة التحرك التلقائي إذا كانت الخطوة الأولى مجرد توجيه (Routing)
            $this->processAutoTransitions($instance, $context);

            return $instance;
        });
    }

    /**
     * تنفيذ مسار (Transition) يدوي صادر من مستخدم (مثل زر الموافقة).
     *
     * @param int $instanceId
     * @param int $transitionId
     * @param array $payloadUpdates
     * @return void
     * @throws BusinessException|\Throwable
     */
    public function transition(int $instanceId, int $transitionId, array $payloadUpdates = []): void
    {
        $this->transaction->execute(function () use ($instanceId, $transitionId, $payloadUpdates) {
            
            // Lock the instance to prevent race conditions
            $instanceRow = $this->db->connection()->selectOne(
                "SELECT * FROM workflow_instances WHERE id = ? FOR UPDATE", 
                [$instanceId]
            );

            if (!$instanceRow || $instanceRow['status'] !== 'active') {
                throw new BusinessException("Workflow instance is either invalid or not active.");
            }

            $transitionRow = $this->db->connection()->selectOne(
                "SELECT * FROM workflow_transitions WHERE id = ?", 
                [$transitionId]
            );

            if (!$transitionRow || (int) $transitionRow['from_step_id'] !== (int) $instanceRow['current_step_id']) {
                throw new BusinessException("Invalid transition for the current workflow step.");
            }

            // Update Context
            $payload = json_decode($instanceRow['payload'], true) ?? [];
            $payload = array_merge($payload, $payloadUpdates);
            $context = new WorkflowContext($instanceId, (int) $instanceRow['entity_id'], $instanceRow['entity_type'], $payload);

            // Validate Conditions
            if (!$this->evaluateConditions($transitionId, $context)) {
                throw new BusinessException("Transition conditions are not met.", 422);
            }

            // Perform State Change
            $this->changeState(new WorkflowInstance($instanceRow), (int) $transitionRow['to_step_id'], $context);
        });
    }

    /**
     * تقييم جميع الشروط المطلوبة لعبور مسار معين.
     *
     * @param int $transitionId
     * @param WorkflowContext $context
     * @return bool
     */
    protected function evaluateConditions(int $transitionId, WorkflowContext $context): bool
    {
        $conditions = $this->repository->getTransitionConditions($transitionId);

        foreach ($conditions as $condition) {
            $field = $condition['field_key'];
            $actualValue = $context->get($field);
            $expectedValue = $condition['expected_value'];
            
            // تحويل النوع للرقمي إذا كان مخزناً كنص في الداتابيز ليتم تقييمه صح
            if (is_numeric($expectedValue)) {
                $expectedValue = (float) $expectedValue;
                $actualValue = (float) $actualValue;
            }

            if (!$this->conditionEvaluator->evaluate($condition['operator'], $expectedValue, $actualValue)) {
                return false; // فشل شرط واحد يوقف المسار (AND Logic)
            }
        }

        return true;
    }

    /**
     * تنفيذ مهام الدخول والخروج من الخطوات.
     *
     * @param int $stepId
     * @param string $timing
     * @param WorkflowContext $context
     * @return void
     */
    protected function executeActions(int $stepId, string $timing, WorkflowContext $context): void
    {
        $actions = $this->repository->getStepActions($stepId, $timing);

        foreach ($actions as $action) {
            $parameters = json_decode($action['parameters'] ?? '{}', true) ?: [];
            $this->actionExecutor->execute($action['action_type'], $parameters, $context);
        }
    }

    /**
     * تغيير حالة الـ Instance لخطوة جديدة وحفظها.
     *
     * @param WorkflowInstance $instance
     * @param int $nextStepId
     * @param WorkflowContext $context
     * @return void
     */
    protected function changeState(WorkflowInstance $instance, int $nextStepId, WorkflowContext $context): void
    {
        $currentStepId = (int) $instance->current_step_id;

        // 1. أفعال الخروج من الخطوة الحالية
        $this->executeActions($currentStepId, 'on_exit', $context);

        // 2. تحديث الداتابيز
        $now = date('Y-m-d H:i:s');
        $payloadJson = json_encode($context->getPayload(), JSON_UNESCAPED_UNICODE);
        
        $this->db->connection()->update(
            "UPDATE workflow_instances SET current_step_id = ?, payload = ?, updated_at = ? WHERE id = ?",
            [$nextStepId, $payloadJson, $now, $instance->id]
        );

        $instance->setAttribute('current_step_id', $nextStepId);
        $instance->setAttribute('payload', $payloadJson);

        // 3. أفعال الدخول للخطوة الجديدة
        $this->executeActions($nextStepId, 'on_enter', $context);

        // 4. فحص ما إذا كانت الخطوة الجديدة توجه تلقائي
        $this->processAutoTransitions($instance, $context);
    }

    /**
     * فحص الشروط تلقائياً. إذا تطابقت مع مسار بدون تدخل بشري، يعبر للخطوة التالية.
     *
     * @param WorkflowInstance $instance
     * @param WorkflowContext $context
     * @return void
     */
    protected function processAutoTransitions(WorkflowInstance $instance, WorkflowContext $context): void
    {
        $stepId = (int) $instance->current_step_id;
        $transitions = $this->repository->getOutgoingTransitions($stepId);

        foreach ($transitions as $transition) {
            if ($this->evaluateConditions((int) $transition['id'], $context)) {
                // وجدنا مساراً شروطه متطابقة، نعبر فوراً
                $this->changeState($instance, (int) $transition['to_step_id'], $context);
                return; // بمجرد عبور مسار واحد نتوقف (First Match Wins)
            }
        }
    }
}