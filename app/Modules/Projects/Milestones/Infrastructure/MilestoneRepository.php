<?php
// Path: app/Modules/Projects/Milestones/Infrastructure/MilestoneRepository.php

declare(strict_types=1);

namespace App\Modules\Projects\Milestones\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Projects\Milestones\Domain\MilestoneRepositoryInterface;
use App\Modules\Projects\Milestones\Domain\Milestone;

class MilestoneRepository extends BaseRepository implements MilestoneRepositoryInterface
{
    protected string $table = 'project_milestones';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getMilestonesForProject(int $projectId, int $companyId): array
    {
        $records = $this->newQuery()
            ->where('project_id', '=', $projectId)
            ->where('company_id', '=', $companyId)
            ->orderBy('due_date', 'asc')
            ->get();

        return array_map(fn($record) => new Milestone($record), $records);
    }
}