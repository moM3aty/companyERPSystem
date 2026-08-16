<?php
// Path: app/Modules/Projects/Milestones/Domain/MilestoneRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Projects\Milestones\Domain;

use App\Core\Contracts\RepositoryInterface;

interface MilestoneRepositoryInterface extends RepositoryInterface
{
    public function getMilestonesForProject(int $projectId, int $companyId): array;
}