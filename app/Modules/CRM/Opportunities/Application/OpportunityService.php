<?php
// Path: app/Modules/CRM/Opportunities/Application/OpportunityService.php

declare(strict_types=1);

namespace App\Modules\CRM\Opportunities\Application;

use App\Modules\CRM\Opportunities\Domain\OpportunityRepositoryInterface;
use App\Core\Database\TransactionManager;

class OpportunityService
{
    protected OpportunityRepositoryInterface $opportunityRepo;
    protected TransactionManager $transaction;

    public function __construct(OpportunityRepositoryInterface $opportunityRepo, TransactionManager $transaction)
    {
        $this->opportunityRepo = $opportunityRepo;
        $this->transaction = $transaction;
    }

    public function createOpportunity(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $data['company_id'] = $companyId;
            $data['created_by'] = $userId;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->opportunityRepo->create($data);
        });
    }
}