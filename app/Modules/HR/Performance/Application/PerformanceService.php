<?php
// Path: app/Modules/HR/Performance/Application/PerformanceService.php

declare(strict_types=1);

namespace App\Modules\HR\Performance\Application;

use App\Modules\HR\Performance\Domain\AppraisalRepositoryInterface;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Performance Management
 */
class PerformanceService
{
    protected AppraisalRepositoryInterface $appraisalRepo;
    protected TransactionManager $transaction;

    public function __construct(AppraisalRepositoryInterface $appraisalRepo, TransactionManager $transaction)
    {
        $this->appraisalRepo = $appraisalRepo;
        $this->transaction = $transaction;
    }

    public function submitAppraisal(array $data, int $companyId, int $evaluatorId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $evaluatorId) {
            
            if ($this->appraisalRepo->hasOverlappingAppraisal((int)$data['employee_id'], $data['period_start'], $data['period_end'], $companyId)) {
                throw new BusinessException("An appraisal already exists for this employee in the specified period.", 409);
            }

            $data['company_id'] = $companyId;
            $data['evaluator_id'] = $evaluatorId;
            $data['status'] = 'submitted';
            $data['goals_achieved'] = isset($data['goals_achieved']) ? json_encode($data['goals_achieved'], JSON_UNESCAPED_UNICODE) : null;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->appraisalRepo->create($data);
        });
    }
}