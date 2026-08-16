Accounting/Domain/Repositories/CostCenterRepositoryInterface.php<?php
// Path: app/Modules/Accounting/Domain/Repositories/CostCenterRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Accounting\Domain\Repositories;

interface CostCenterRepositoryInterface
{
    public function getAll(int $companyId): array;
    
    public function findById(int $id, int $companyId): ?array;
    
    public function create(array $data, int $companyId): int;
    
    public function update(int $id, array $data, int $companyId): bool;
    
    /**
     * الحصول على الأرباح/الخسائر الخاصة بمركز تكلفة محدد
     */
    public function getCostCenterProfitAndLoss(int $costCenterId, int $companyId, string $fromDate, string $toDate): array;
}