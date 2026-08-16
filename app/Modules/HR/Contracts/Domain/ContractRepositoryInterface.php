<?php
// Path: app/Modules/HR/Contracts/Domain/ContractRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Contracts\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Contract
 */
interface ContractRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب العقد النشط الحالي للموظف.
     *
     * @param int $employeeId
     * @param int $companyId
     * @return Contract|null
     */
    public function getActiveContract(int $employeeId, int $companyId): ?Contract;

    /**
     * إبطال كافة العقود القديمة للموظف لضمان وجود عقد واحد نشط فقط.
     *
     * @param int $employeeId
     * @param int $companyId
     * @return void
     */
    public function deactivatePreviousContracts(int $employeeId, int $companyId): void;
}