<?php
// Path: app/Modules/Projects/Expenses/Infrastructure/ProjectExpenseRepository.php

declare(strict_types=1);

namespace App\Modules\Projects\Expenses\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Projects\Expenses\Domain\ProjectExpenseRepositoryInterface;

class ProjectExpenseRepository extends BaseRepository implements ProjectExpenseRepositoryInterface
{
    protected string $table = 'project_expenses';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getTotalExpensesForProject(int $projectId, int $companyId): float
    {
        $result = $this->db->connection()->selectOne(
            "SELECT SUM(amount) as total FROM {$this->table} WHERE project_id = ? AND company_id = ? AND status = 'approved'",
            [$projectId, $companyId]
        );

        return (float) ($result['total'] ?? 0.0);
    }
}