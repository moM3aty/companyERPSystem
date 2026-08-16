<?php
// Path: app/Modules/HR/Onboarding/Application/OnboardingService.php

declare(strict_types=1);

namespace App\Modules\HR\Onboarding\Application;

use App\Modules\HR\Onboarding\Domain\OnboardingTaskRepositoryInterface;
use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class OnboardingService
{
    protected OnboardingTaskRepositoryInterface $taskRepo;
    protected DatabaseManager $db;

    public function __construct(OnboardingTaskRepositoryInterface $taskRepo, DatabaseManager $db)
    {
        $this->taskRepo = $taskRepo;
        $this->db = $db;
    }

    public function completeTask(int $taskId, int $userId, int $companyId): void
    {
        $this->taskRepo->setTenantId($companyId);
        $task = $this->taskRepo->findOrFail($taskId);

        // التحقق من أن المستخدم الحالي هو المكلف بالمهمة (إجراء أمني)
        if ((int) $task['assigned_to'] !== $userId) {
            throw new BusinessException("You are not authorized to complete this onboarding task.", 403);
        }

        if ($task['status'] === 'completed') {
            throw new BusinessException("This task is already completed.");
        }

        $this->taskRepo->update($taskId, [
            'status'       => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s')
        ]);
    }
}