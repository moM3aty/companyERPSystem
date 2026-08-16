<?php
// Path: app/Modules/Projects/Expenses/Application/ProjectExpenseService.php

declare(strict_types=1);

namespace App\Modules\Projects\Expenses\Application;

use App\Core\Database\TransactionManager;
use App\Modules\Projects\Expenses\Domain\ProjectExpenseRepositoryInterface;

class ProjectExpenseService
{
    protected ProjectExpenseRepositoryInterface $expenseRepo;
    protected TransactionManager $transaction;

    public function __construct(ProjectExpenseRepositoryInterface $expenseRepo, TransactionManager $transaction)
    {
        $this->expenseRepo = $expenseRepo;
        $this->transaction = $transaction;
    }

    public function logExpense(array $data, int $companyId, int $employeeId): int
    {
        return $this->transaction->execute(function () use ($data, $companyId, $employeeId) {
            $data['company_id'] = $companyId;
            $data['employee_id'] = $employeeId;
            $data['status']     = 'pending';
            $data['created_at'] = date('Y-m-d H:i:s');

            return $this->expenseRepo->create($data);
        });
    }
}