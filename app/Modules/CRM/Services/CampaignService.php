<?php
// Path: app/Modules/CRM/Services/CampaignService.php

declare(strict_types=1);

namespace App\Modules\CRM\Services;

use App\Modules\CRM\Repositories\CampaignRepository;
use App\Core\Database\TransactionManager;

class CampaignService
{
    protected CampaignRepository $campaignRepo;
    protected TransactionManager $transaction;

    public function __construct(CampaignRepository $campaignRepo, TransactionManager $transaction)
    {
        $this->campaignRepo = $campaignRepo;
        $this->transaction = $transaction;
    }

    public function createCampaign(array $data, int $companyId, int $userId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $userId) {
            
            $data['company_id'] = $companyId;
            $data['status']     = 'planned';
            $data['created_by'] = $userId;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->campaignRepo->create($data);
        });
    }
}