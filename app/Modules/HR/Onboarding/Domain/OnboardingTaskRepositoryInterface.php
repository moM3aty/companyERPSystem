<?php
// Path: app/Modules/HR/Onboarding/Domain/OnboardingTaskRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Onboarding\Domain;

use App\Core\Contracts\RepositoryInterface;

interface OnboardingTaskRepositoryInterface extends RepositoryInterface
{
    public function getPendingTasksForEmployee(int $employeeId, int $companyId): array;
}