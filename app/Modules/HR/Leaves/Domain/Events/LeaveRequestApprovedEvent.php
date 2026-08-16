<?php
// Path: app/Modules/HR/Leaves/Domain/Events/LeaveRequestApprovedEvent.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Leave Request Approved
 * يفيد موديول الـ Payroll لإيقاف الراتب في حال كانت الإجازة غير مدفوعة (Unpaid).
 */
class LeaveRequestApprovedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $employeeId;
    public readonly string $leaveType;

    public function __construct(int $leaveRequestId, int $companyId, int $employeeId, string $leaveType)
    {
        parent::__construct($leaveRequestId);
        $this->companyId = $companyId;
        $this->employeeId = $employeeId;
        $this->leaveType = $leaveType;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'employee_id' => $this->employeeId,
            'leave_type'  => $this->leaveType,
        ]);
    }
}