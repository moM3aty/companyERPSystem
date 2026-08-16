<?php
// Path: app/Modules/HR/Leaves/Application/LeaveService.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Application;

use App\Modules\HR\Leaves\Domain\LeaveRequestRepositoryInterface;
use App\Modules\HR\Leaves\Domain\Events\LeaveRequestApprovedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;
use App\Core\Helpers\Date;

class LeaveService
{
    protected LeaveRequestRepositoryInterface $leaveRepo;
    protected TransactionManager $transaction;
    protected EventBus $eventBus;

    public function __construct(
        LeaveRequestRepositoryInterface $leaveRepo,
        TransactionManager $transaction,
        EventBus $eventBus
    ) {
        $this->leaveRepo = $leaveRepo;
        $this->transaction = $transaction;
        $this->eventBus = $eventBus;
    }

    public function submitLeaveRequest(array $data, int $companyId): int
    {
        // 1. التحقق من التداخل
        if ($this->leaveRepo->hasOverlappingLeave((int)$data['employee_id'], $data['start_date'], $data['end_date'])) {
            throw new BusinessException("The employee already has a pending or approved leave during this period.", 409);
        }

        // 2. حساب الأيام
        $totalDays = Date::diffInDays($data['start_date'], $data['end_date']) + 1; // Inclusive

        $data['company_id'] = $companyId;
        $data['total_days'] = $totalDays;
        $data['status']     = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->leaveRepo->create($data);
    }

    public function approveLeave(int $leaveRequestId, int $approverId, int $companyId): void
    {
        $this->transaction->execute(function () use ($leaveRequestId, $approverId, $companyId) {
            $this->leaveRepo->setTenantId($companyId);
            $leave = $this->leaveRepo->findOrFail($leaveRequestId);

            if ($leave['status'] !== 'pending') {
                throw new BusinessException("Only pending leave requests can be approved.");
            }

            $this->leaveRepo->update($leaveRequestId, [
                'status'      => 'approved',
                'approved_by' => $approverId,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            $this->eventBus->publish(new LeaveRequestApprovedEvent(
                $leaveRequestId,
                $companyId,
                (int) $leave['employee_id'],
                $leave['leave_type']
            ));
        });
    }
}