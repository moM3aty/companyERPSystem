<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Repositories/CostCenterRepository.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Repositories;

use App\Modules\Accounting\Domain\Repositories\CostCenterRepositoryInterface;
use App\Modules\Accounting\Infrastructure\Persistence\Models\CostCenterModel;

class CostCenterRepository implements CostCenterRepositoryInterface
{
    private CostCenterModel $model;

    public function __construct()
    {
        $this->model = new CostCenterModel();
    }

    public function getAll(int $companyId): array
    {
        return $this->model->fetchAll($companyId);
    }

    public function findById(int $id, int $companyId): ?array { return null; }
    public function create(array $data, int $companyId): int { return 0; }
    public function update(int $id, array $data, int $companyId): bool { return false; }
    public function getCostCenterProfitAndLoss(int $costCenterId, int $companyId, string $fromDate, string $toDate): array { return []; }
}