<?php
// Path: app/Modules/HR/Employees/Domain/Events/EmployeeCreatedEvent.php

declare(strict_types=1);

namespace App\Modules\HR\Employees\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Employee Created
 * يتم إطلاقه فور تعيين موظف جديد لتقوم إدارة الـ IT بإنشاء حساب له، والـ Payroll بتهيئة ملفه.
 */
class EmployeeCreatedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly string $employeeCode;

    public function __construct(int $employeeId, int $companyId, string $employeeCode)
    {
        parent::__construct($employeeId);
        $this->companyId = $companyId;
        $this->employeeCode = $employeeCode;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'    => $this->companyId,
            'employee_code' => $this->employeeCode,
        ]);
    }
}