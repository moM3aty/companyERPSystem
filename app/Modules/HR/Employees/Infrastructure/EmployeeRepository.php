<?php
// Path: app/Modules/HR/Employees/Infrastructure/EmployeeRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Employees\Domain\Employee;
use App\Modules\HR\Employees\Domain\EmployeeRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Employee
 */
class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    protected string $table = 'hr_employees';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByCode(string $employeeCode, int $companyId): ?Employee
    {
        $data = $this->newQuery()
                     ->where('employee_code', '=', $employeeCode)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new Employee($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function generateEmployeeCode(int $companyId): string
    {
        $prefix = 'EMP-';
        
        $lastEmployee = $this->newQuery()
            ->select(['employee_code'])
            ->where('company_id', '=', $companyId)
            ->where('employee_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastEmployee) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastEmployee['employee_code']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }
}