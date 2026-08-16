<?php
// Path: app/Modules/Accounting/Application/Services/CostCenterService.php

declare(strict_types=1);

namespace App\Modules\Accounting\Application\Services;

use App\Modules\Accounting\Domain\Repositories\CostCenterRepositoryInterface;

class CostCenterService
{
    public function __construct(
        private readonly CostCenterRepositoryInterface $costCenterRepository
    ) {}

    public function getAllCostCenters(int $companyId): array
    {
        return $this->costCenterRepository->getAll($companyId);
    }

    public function getProfitAndLoss(int $costCenterId, int $companyId, string $fromDate, string $toDate): array
    {
        return $this->costCenterRepository->getCostCenterProfitAndLoss($costCenterId, $companyId, $fromDate, $toDate);
    }
}