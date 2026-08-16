<?php
// Path: app/Modules/HR/Contracts/Application/ContractService.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Application;

use App\Modules\HR\Contracts\Domain\Contract;
use App\Modules\HR\Contracts\Domain\ContractRepositoryInterface;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Contract
 * المحرك المسؤول عن إصدار عقود العمل.
 */
class ContractService
{
    protected ContractRepositoryInterface $contractRepo;
    protected TransactionManager $transaction;

    public function __construct(ContractRepositoryInterface $contractRepo, TransactionManager $transaction)
    {
        $this->contractRepo = $contractRepo;
        $this->transaction = $transaction;
    }

    /**
     * إنشاء أو تجديد عقد موظف.
     * يضمن النظام عدم وجود أكثر من عقد نشط لنفس الموظف في نفس اللحظة.
     *
     * @param array $data
     * @param int $companyId
     * @return Contract
     * @throws \Throwable
     */
    public function issueContract(array $data, int $companyId): Contract
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            // 1. Deactivate old contracts for this employee
            $this->contractRepo->deactivatePreviousContracts((int) $data['employee_id'], $companyId);

            // 2. Issue the new active contract
            $data['company_id'] = $companyId;
            $data['status'] = 'active';

            $contractId = $this->contractRepo->create($data);

            $this->contractRepo->setTenantId($companyId);
            $contractData = $this->contractRepo->findOrFail($contractId);
            
            return new Contract($contractData);
        });
    }
}