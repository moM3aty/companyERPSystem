<?php
// Path: app/Modules/CRM/FollowUps/Application/FollowUpService.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Application;

use App\Modules\CRM\FollowUps\Domain\FollowUpRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class FollowUpService
{
    protected FollowUpRepositoryInterface $repo;
    protected TransactionManager $transaction;

    public function __construct(FollowUpRepositoryInterface $repo, TransactionManager $transaction)
    {
        $this->repo = $repo;
        $this->transaction = $transaction;
    }

    public function scheduleFollowUp(array $data, int $companyId, int $userId): int
    {
        $data['company_id'] = $companyId;
        $data['status']     = 'pending';
        $data['created_by'] = $userId;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->repo->create($data);
    }

    public function completeFollowUp(int $id, int $companyId, int $userId): void
    {
        $this->transaction->execute(function () use ($id, $companyId, $userId) {
            $this->repo->setTenantId($companyId);
            $followUp = $this->repo->findOrFail($id);

            if ($followUp['status'] !== 'pending') {
                throw new BusinessException("This follow-up is already processed.", 409);
            }

            // التحقق أن من يغلق המتابعة هو إما المندوب نفسه أو مدير مبيعات له صلاحية عالية (لتبسيط العملية نعتمد على ה-Gate)
            
            $this->repo->update($id, [
                'status'       => 'completed',
                'completed_at' => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s')
            ]);
        });
    }
}