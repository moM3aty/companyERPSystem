<?php
// Path: app/Modules/Purchasing/Suppliers/Application/SupplierService.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Application;

use App\Modules\Purchasing\Suppliers\Domain\Supplier;
use App\Modules\Purchasing\Suppliers\Domain\SupplierRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Supplier
 */
class SupplierService
{
    protected SupplierRepositoryInterface $supplierRepo;
    protected TransactionManager $transaction;

    public function __construct(SupplierRepositoryInterface $supplierRepo, TransactionManager $transaction)
    {
        $this->supplierRepo = $supplierRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء مورد جديد.
     *
     * @param array $data
     * @param int $companyId
     * @return Supplier
     * @throws \Throwable
     */
    public function createSupplier(array $data, int $companyId): Supplier
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['is_active'] = $data['is_active'] ?? 1;

            $supplierId = $this->supplierRepo->create($data);

            $this->supplierRepo->setTenantId($companyId);
            $supplierData = $this->supplierRepo->findOrFail($supplierId);

            return new Supplier($supplierData);
        });
    }
}