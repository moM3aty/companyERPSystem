<?php
// Path: app/Modules/Payroll/Services/SalaryStructureService.php

declare(strict_types=1);

namespace App\Modules\Payroll\Services;

use App\Core\Database\TransactionManager;
use App\Modules\Payroll\Repositories\SalaryStructureRepository;

/**
 * Enterprise Application Service: Salary Structure
 * يدير معالجة بناء هياكل الرواتب وتوزيع النسب بأمان تام (Atomic Transaction).
 */
class SalaryStructureService
{
    protected SalaryStructureRepository $repository;
    protected TransactionManager $transaction;

    public function __construct(SalaryStructureRepository $repository, TransactionManager $transaction)
    {
        $this->repository = $repository;
        $this->transaction = $transaction;
    }

    public function createStructure(array $headerData, array $components, int $companyId): int
    {
        return $this->transaction->execute(function () use ($headerData, $components, $companyId) {
            
            $headerData['company_id'] = $companyId;
            $headerData['is_active']  = $headerData['is_active'] ?? 1;
            $headerData['created_at'] = date('Y-m-d H:i:s');
            
            return $this->repository->saveWithComponents($headerData, $components);
        });
    }
}