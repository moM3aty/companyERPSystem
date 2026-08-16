<?php
// Path: app/Modules/CRM/Leads/Application/LeadService.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Application;

use App\Modules\CRM\Leads\Domain\LeadRepositoryInterface;
use App\Core\Database\TransactionManager;

class LeadService
{
    protected LeadRepositoryInterface $leadRepo;
    protected TransactionManager $transaction;

    public function __construct(LeadRepositoryInterface $leadRepo, TransactionManager $transaction)
    {
        $this->leadRepo = $leadRepo;
        $this->transaction = $transaction;
    }

    public function createLead(array $data, int $companyId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            $data['company_id'] = $companyId;
            $data['status'] = 'new';
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->leadRepo->create($data);
        });
    }
}