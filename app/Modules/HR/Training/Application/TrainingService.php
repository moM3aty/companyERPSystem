<?php
// Path: app/Modules/HR/Training/Application/TrainingService.php

declare(strict_types=1);

namespace App\Modules\HR\Training\Application;

use App\Modules\HR\Training\Domain\TrainingRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class TrainingService
{
    protected TrainingRepositoryInterface $trainingRepo;
    protected TransactionManager $transaction;

    public function __construct(TrainingRepositoryInterface $trainingRepo, TransactionManager $transaction)
    {
        $this->trainingRepo = $trainingRepo;
        $this->transaction = $transaction;
    }

    public function createProgram(array $data, int $companyId, int $userId): int
    {
        $data['company_id'] = $companyId;
        $data['status']     = 'planned';
        $data['created_by'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->trainingRepo->create($data);
    }

    public function enroll(int $programId, int $employeeId, int $companyId): void
    {
        $this->transaction->execute(function () use ($programId, $employeeId, $companyId) {
            
            $this->trainingRepo->setTenantId($companyId);
            $program = $this->trainingRepo->findOrFail($programId);

            if ($program['status'] === 'cancelled' || $program['status'] === 'completed') {
                throw new BusinessException("Cannot enroll in a program that is cancelled or completed.");
            }

            $currentCount = $this->trainingRepo->getEnrollmentCount($programId);
            $maxParticipants = (int) $program['max_participants'];

            if ($currentCount >= $maxParticipants) {
                throw new BusinessException("Training program has reached maximum capacity ({$maxParticipants}).");
            }

            // In a real system, you'd also check if the employee is already enrolled to prevent duplicates
            $this->trainingRepo->enrollEmployee($programId, $employeeId);
        });
    }
}