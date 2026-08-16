<?php
// Path: app/Modules/HR/Attendance/Domain/AttendanceRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Domain;

use App\Core\Contracts\RepositoryInterface;

interface AttendanceRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب سجل حضور الموظف في يوم محدد.
     *
     * @param int $employeeId
     * @param string $date
     * @param int $companyId
     * @return array|null
     */
    public function findByEmployeeAndDate(int $employeeId, string $date, int $companyId): ?array;
}