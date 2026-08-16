<?php
// Path: app/Modules/Projects/Expenses/Domain/ProjectExpenseRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Projects\Expenses\Domain;

use App\Core\Contracts\RepositoryInterface;

interface ProjectExpenseRepositoryInterface extends RepositoryInterface
{
    public function getTotalExpensesForProject(int $projectId, int $companyId): float;
}