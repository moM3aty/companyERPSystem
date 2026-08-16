<?php
// Path: app/Modules/Inventory/Warehouses/Application/WarehouseService.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Application;

use App\Modules\Inventory\Warehouses\Domain\Warehouse;
use App\Modules\Inventory\Warehouses\Domain\WarehouseRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Warehouse
 */
class WarehouseService
{
    protected WarehouseRepositoryInterface $repository;
    protected TransactionManager $transaction;

    public function __construct(WarehouseRepositoryInterface $repository, TransactionManager $transaction)
    {
        $this->repository = $repository;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء مستودع جديد.
     *
     * @param array $data
     * @param int $companyId
     * @return Warehouse
     * @throws \Throwable
     */
    public function createWarehouse(array $data, int $companyId): Warehouse
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['is_active'] = $data['is_active'] ?? 1;
            $data['is_transit'] = $data['is_transit'] ?? 0;

            $warehouseId = $this->repository->create($data);

            $this->repository->setTenantId($companyId);
            $warehouseData = $this->repository->findOrFail($warehouseId);

            return new Warehouse($warehouseData);
        });
    }
}