<?php
// Path: app/Modules/HR/Employees/Application/EmployeeService.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Application;

use App\Modules\HR\Employees\Domain\Employee;
use App\Modules\HR\Employees\Domain\EmployeeRepositoryInterface;
use App\Modules\HR\Employees\Domain\Events\EmployeeCreatedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;

/**
 * Enterprise Application Service: Employee
 * يعالج منطق الأعمال لإنشاء الموظف (الرقم التسلسلي الآلي، إطلاق الأحداث).
 */
class EmployeeService
{
    protected EmployeeRepositoryInterface $employeeRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->employeeRepo = $employeeRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    /**
     * تعيين موظف جديد.
     *
     * @param array $data
     * @param int $companyId
     * @return Employee
     * @throws \Throwable
     */
    public function createEmployee(array $data, int $companyId): Employee
    {
        return $this->transaction->execute(function () use ($data, $companyId) {
            
            $data['company_id'] = $companyId;
            $data['status'] = 'active';
            
            // Generate Code securely using the repository
            if (empty($data['employee_code'])) {
                $data['employee_code'] = $this->employeeRepo->generateEmployeeCode($companyId);
            }

            $employeeId = $this->employeeRepo->create($data);

            $this->employeeRepo->setTenantId($companyId);
            $employeeData = $this->employeeRepo->findOrFail($employeeId);
            
            $employee = new Employee($employeeData);

            // Notify System (IT & Payroll Modules)
            $this->eventBus->publish(new EmployeeCreatedEvent(
                $employeeId, 
                $companyId, 
                $employee->getAttribute('employee_code')
            ));

            return $employee;
        });
    }
}