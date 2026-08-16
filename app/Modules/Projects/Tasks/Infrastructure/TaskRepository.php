<?php
// Path: app/Modules/Projects/Tasks/Infrastructure/TaskRepository.php

declare(strict_types=1);

namespace App\Modules\Projects\Tasks\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Projects\Tasks\Domain\TaskRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Project Task
 * المهام تتبع للمشروع، لذلك لا نستخدم الـ TenantScope هنا مباشرة، بل نعتمد على صلاحية الوصول للمشروع.
 */
class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    protected string $table = 'project_tasks';
    protected bool $useTenantScope = false; 

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getTasksByProject(int $projectId): array
    {
        return $this->newQuery()
                    ->where('project_id', '=', $projectId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}