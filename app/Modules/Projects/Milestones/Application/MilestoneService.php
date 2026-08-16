<?php
// Path: app/Modules/Projects/Milestones/Application/MilestoneService.php

declare(strict_types=1);

namespace App\Modules\Projects\Milestones\Application;

use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;
use App\Modules\Projects\Milestones\Domain\MilestoneRepositoryInterface;

class MilestoneService
{
    protected MilestoneRepositoryInterface $repo;
    protected TransactionManager $transaction;

    public function __construct(MilestoneRepositoryInterface $repo, TransactionManager $transaction)
    {
        $this->repo = $repo;
        $this->transaction = $transaction;
    }

    public function createMilestone(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            $data['company_id'] = $companyId;
            $data['status']     = 'pending';
            $data['created_by'] = $userId;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->repo->create($data);
        });
    }

    public function achieveMilestone(int $milestoneId, int $companyId): void
    {
        $this->transaction->execute(function () use ($milestoneId, $companyId) {
            $this->repo->setTenantId($companyId);
            $milestone = $this->repo->findOrFail($milestoneId);

            if ($milestone['status'] === 'achieved') {
                throw new BusinessException("Milestone is already achieved.");
            }

            $this->repo->update($milestoneId, [
                'status'     => 'achieved',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        });
    }
}