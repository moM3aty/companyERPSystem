<?php
// Path: app/Modules/HR/Employees/Domain/EmployeeRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Employee
 */
interface EmployeeRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن موظف بواسطة الكود الوظيفي.
     *
     * @param string $employeeCode
     * @param int $companyId
     * @return Employee|null
     */
    public function findByCode(string $employeeCode, int $companyId): ?Employee;

    /**
     * توليد كود وظيفي جديد متسلسل آلياً.
     *
     * @param int $companyId
     * @return string
     */
    public function generateEmployeeCode(int $companyId): string;
}