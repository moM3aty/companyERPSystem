<?php
// Path: app/Modules/Projects/Tasks/Domain/TaskRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Projects\Tasks\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Project Task
 */
interface TaskRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب جميع المهام الخاصة بمشروع معين.
     *
     * @param int $projectId
     * @return array
     */
    public function getTasksByProject(int $projectId): array;
}