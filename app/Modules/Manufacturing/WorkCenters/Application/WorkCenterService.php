<?php
// Path: app/Modules/Manufacturing/WorkCenters/Application/WorkCenterService.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\WorkCenters\Application;

use App\Modules\Manufacturing\WorkCenters\Infrastructure\WorkCenterRepository;
use App\Core\Database\TransactionManager;

class WorkCenterService
{
    protected WorkCenterRepository $workCenterRepo;
    protected TransactionManager $transaction;

    public function __construct(WorkCenterRepository $workCenterRepo, TransactionManager $transaction)
    {
        $this->workCenterRepo = $workCenterRepo;
        $this->transaction = $transaction;
    }

    public function createWorkCenter(array $data, int $companyId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            $data['company_id'] = $companyId;
            $data['is_active']  = $data['is_active'] ?? 1;
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->workCenterRepo->create($data);
        });
    }
}