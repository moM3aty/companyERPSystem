<?php
// Path: app/Modules/Administration/Branches/Application/BranchService.php

declare(strict_types=1);

namespace App\Modules\Administration\Branches\Application;

use App\Core\Database\TransactionManager;
use App\Modules\Administration\Branches\Domain\BranchRepositoryInterface;
use App\Core\Tenant\Branch;

class BranchService
{
    protected BranchRepositoryInterface $branchRepo;
    protected TransactionManager $transaction;

    public function __construct(BranchRepositoryInterface $branchRepo, TransactionManager $transaction)
    {
        $this->branchRepo = $branchRepo;
        $this->transaction = $transaction;
    }

    public function createBranch(array $data, int $companyId): Branch
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            $data['company_id'] = $companyId;
            $data['is_active'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');

            $branchId = $this->branchRepo->create($data);

            $this->branchRepo->setTenantId($companyId);
            $branchData = $this->branchRepo->findOrFail($branchId);

            return new Branch($branchData);
        });
    }
}