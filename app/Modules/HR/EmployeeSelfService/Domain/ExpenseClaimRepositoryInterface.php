<?php
// Path: app/Modules/HR/EmployeeSelfService/Domain/ExpenseClaimRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Domain;

use App\Core\Contracts\RepositoryInterface;

interface ExpenseClaimRepositoryInterface extends RepositoryInterface
{
    public function generateClaimNumber(int $companyId): string;
    public function bulkInsertItems(int $claimId, array $items): void;
}