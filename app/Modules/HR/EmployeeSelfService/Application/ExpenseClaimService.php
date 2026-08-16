<?php
// Path: app/Modules/HR/EmployeeSelfService/Application/ExpenseClaimService.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Application;

use App\Modules\HR\EmployeeSelfService\Domain\ExpenseClaimRepositoryInterface;
use App\Core\Database\TransactionManager;

class ExpenseClaimService
{
    protected ExpenseClaimRepositoryInterface $claimRepo;
    protected TransactionManager $transaction;

    public function __construct(ExpenseClaimRepositoryInterface $claimRepo, TransactionManager $transaction)
    {
        $this->claimRepo = $claimRepo;
        $this->transaction = $transaction;
    }

    public function submitClaim(array $headerData, array $itemsData, int $employeeId, int $companyId): int
    {
        return $this->transaction->execute(function () use ($headerData, $itemsData, $employeeId, $companyId) {
            
            $totalAmount = 0.0;
            foreach ($itemsData as $item) {
                $totalAmount += (float) $item['amount'];
            }

            $claimData = [
                'company_id'   => $companyId,
                'employee_id'  => $employeeId,
                'claim_no'     => $this->claimRepo->generateClaimNumber($companyId),
                'claim_date'   => $headerData['claim_date'],
                'total_amount' => $totalAmount,
                'currency_id'  => $headerData['currency_id'],
                'purpose'      => $headerData['purpose'],
                'status'       => 'pending', // يوجه لاحقاً لدورة الموافقات Workflow
                'created_at'   => date('Y-m-d H:i:s')
            ];

            $claimId = $this->claimRepo->create($claimData);
            $this->claimRepo->bulkInsertItems($claimId, $itemsData);

            // في النظام الحقيقي نطلق حدث: EventBus::publish(new ExpenseClaimSubmittedEvent(...));

            return $claimId;
        });
    }
}