<?php
// Path: app/Core/Workflow/WorkflowRepository.php

declare(strict_types=1);

namespace App\Core\Workflow;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Workflow Repository
 * يتعامل مع استرجاع هيكلة سير العمل (الإصدارات، الخطوات، المسارات، والشروط).
 */
class WorkflowRepository
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * جلب أحدث إصدار مفعل لسير عمل معين.
     *
     * @param string $code
     * @param int $companyId
     * @return WorkflowVersion|null
     */
    public function getActiveVersion(string $code, int $companyId): ?WorkflowVersion
    {
        $sql = "SELECT wv.* FROM workflow_versions wv
                JOIN workflow_definitions wd ON wv.workflow_definition_id = wd.id
                WHERE wd.code = ? AND wd.company_id = ? AND wd.is_active = 1 AND wv.status = 'published'
                ORDER BY wv.version_number DESC LIMIT 1";

        $data = $this->db->connection()->selectOne($sql, [$code, $companyId]);

        return $data ? new WorkflowVersion($data) : null;
    }

    /**
     * جلب الخطوة الابتدائية للإصدار.
     *
     * @param int $versionId
     * @return WorkflowStep|null
     */
    public function getStartStep(int $versionId): ?WorkflowStep
    {
        $sql = "SELECT * FROM workflow_steps WHERE workflow_version_id = ? AND is_start_step = 1 LIMIT 1";
        $data = $this->db->connection()->selectOne($sql, [$versionId]);

        return $data ? new WorkflowStep($data) : null;
    }

    /**
     * جلب جميع المسارات المتاحة للخروج من خطوة معينة.
     *
     * @param int $stepId
     * @return array
     */
    public function getOutgoingTransitions(int $stepId): array
    {
        $sql = "SELECT * FROM workflow_transitions WHERE from_step_id = ?";
        return $this->db->connection()->select($sql, [$stepId]);
    }

    /**
     * جلب الشروط المرتبطة بمسار (Transition) معين.
     *
     * @param int $transitionId
     * @return array
     */
    public function getTransitionConditions(int $transitionId): array
    {
        $sql = "SELECT * FROM workflow_conditions WHERE transition_id = ?";
        return $this->db->connection()->select($sql, [$transitionId]);
    }

    /**
     * جلب الأفعال (Actions) المرتبطة بخطوة معينة (عند الدخول أو الخروج).
     *
     * @param int $stepId
     * @param string $timing 'on_enter' or 'on_exit'
     * @return array
     */
    public function getStepActions(int $stepId, string $timing): array
    {
        $sql = "SELECT * FROM workflow_actions WHERE step_id = ? AND trigger_timing = ?";
        return $this->db->connection()->select($sql, [$stepId, $timing]);
    }
}