<?php
// Path: app/Modules/HR/Attendance/Application/AttendanceService.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Application;

use App\Modules\HR\Attendance\Domain\AttendanceRepositoryInterface;
use App\Core\Exceptions\BusinessException;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Attendance
 * يعالج تسجيل بصمة الموظف (In/Out) ويمنع الازدواجية في نفس اليوم.
 */
class AttendanceService
{
    protected AttendanceRepositoryInterface $attendanceRepo;
    protected TransactionManager $transaction;

    public function __construct(AttendanceRepositoryInterface $attendanceRepo, TransactionManager $transaction)
    {
        $this->attendanceRepo = $attendanceRepo;
        $this->transaction = $transaction;
    }

    /**
     * تسجيل الحضور أو الانصراف (بصمة واحدة للتبسيط).
     *
     * @param int $employeeId
     * @param int $companyId
     * @return array رسالة وحالة العملية
     * @throws BusinessException|\Throwable
     */
    public function punch(int $employeeId, int $companyId): array
    {
        $today = date('Y-m-d');
        $nowTime = date('H:i:s');

        return $this->transaction->execute(function () use ($employeeId, $companyId, $today, $nowTime) {
            
            $record = $this->attendanceRepo->findByEmployeeAndDate($employeeId, $today, $companyId);

            if (!$record) {
                // أول بصمة اليوم (Check-In)
                $data = [
                    'company_id'    => $companyId,
                    'employee_id'   => $employeeId,
                    'record_date'   => $today,
                    'check_in_time' => $nowTime,
                    'status'        => 'present',
                    'late_minutes'  => 0, // في نظام معقد تُحسب بناءً على وردية الموظف (Shift)
                    'created_at'    => date('Y-m-d H:i:s')
                ];
                
                $this->attendanceRepo->create($data);
                return ['status' => 'check_in', 'message' => "Checked in successfully at {$nowTime}"];
            }

            if (!empty($record['check_out_time'])) {
                throw new BusinessException("Employee has already checked out for today.");
            }

            // بصمة ثانية (Check-Out)
            $this->attendanceRepo->update((int) $record['id'], [
                'check_out_time' => $nowTime,
                'updated_at'     => date('Y-m-d H:i:s')
            ]);

            return ['status' => 'check_out', 'message' => "Checked out successfully at {$nowTime}"];
        });
    }
}