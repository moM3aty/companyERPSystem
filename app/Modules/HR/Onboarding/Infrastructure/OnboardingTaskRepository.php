<?php
// Path: app/Modules/HR/Onboarding/Infrastructure/OnboardingTaskRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Onboarding\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Onboarding\Domain\OnboardingTaskRepositoryInterface;

class OnboardingTaskRepository extends BaseRepository implements OnboardingTaskRepositoryInterface
{
    protected string $table = 'hr_onboarding_tasks';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getPendingTasksForEmployee(int $employeeId, int $companyId): array
    {
        return $this->newQuery()
            ->where('employee_id', '=', $employeeId)
            ->where('company_id', '=', $companyId)
            ->where('status', '=', 'pending')
            ->get();
    }
}