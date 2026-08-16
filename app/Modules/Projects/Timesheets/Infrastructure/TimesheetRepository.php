<?php
// Path: app/Modules/Projects/Timesheets/Infrastructure/TimesheetRepository.php

declare(strict_types=1);

namespace App\Modules\Projects\Timesheets\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Projects\Timesheets\Domain\TimesheetRepositoryInterface;

class TimesheetRepository extends BaseRepository implements TimesheetRepositoryInterface
{
    protected string $table = 'project_timesheets';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getTotalHoursForTask(int $taskId): float
    {
        $result = $this->db->connection()->selectOne(
            "SELECT SUM(hours) as total_hours FROM {$this->table} WHERE task_id = ? AND status IN ('submitted', 'approved')",
            [$taskId]
        );

        return (float) ($result['total_hours'] ?? 0.0);
    }
}