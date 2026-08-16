<?php
// Path: app/Modules/Projects/Budgets/Application/ProjectBudgetService.php

declare(strict_types=1);

namespace App\Modules\Projects\Budgets\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Project Budgeting
 * يحدد الميزانية المخصصة لمشروع معين ويراقب النفقات الخاصة به.
 */
class ProjectBudgetService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function allocateBudget(int $projectId, float $amount, int $companyId): void
    {
        $project = $this->db->connection()->selectOne(
            "SELECT id FROM projects WHERE id = ? AND company_id = ?",
            [$projectId, $companyId]
        );

        if (!$project) {
            throw new BusinessException("Project not found.");
        }

        if ($amount <= 0) {
            throw new BusinessException("Budget amount must be strictly positive.");
        }

        $this->db->connection()->update(
            "UPDATE projects SET budget = ?, updated_at = ? WHERE id = ?",
            [$amount, date('Y-m-d H:i:s'), $projectId]
        );

        // توثيق التخصيص في جدول موازنات المشاريع للتاريخ
        $this->db->connection()->insert(
            "INSERT INTO project_budgets (company_id, project_id, allocated_amount, created_at) VALUES (?, ?, ?, ?)",
            [$companyId, $projectId, $amount, date('Y-m-d H:i:s')]
        );
    }
}