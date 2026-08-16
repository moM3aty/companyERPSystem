<?php
// Path: app/Modules/HR/Attendance/Infrastructure/AttendanceRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Attendance\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Attendance\Domain\AttendanceRepositoryInterface;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    protected string $table = 'hr_attendance_records';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function findByEmployeeAndDate(int $employeeId, string $date, int $companyId): ?array
    {
        $result = $this->newQuery()
            ->where('employee_id', '=', $employeeId)
            ->where('record_date', '=', $date)
            ->where('company_id', '=', $companyId)
            ->first();

        return $result ?: null;
    }
}