<?php
// Path: app/Modules/HR/Leaves/Infrastructure/LeaveRequestRepository.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\Leaves\Domain\LeaveRequestRepositoryInterface;

class LeaveRequestRepository extends BaseRepository implements LeaveRequestRepositoryInterface
{
    protected string $table = 'hr_leave_requests';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate): bool
    {
        // التحقق من التداخل: إجازة جديدة تبدأ قبل انتهاء القديمة وتنتهي بعد بداية القديمة
        $sql = "SELECT id FROM {$this->table} 
                WHERE employee_id = ? 
                  AND status IN ('pending', 'approved') 
                  AND start_date <= ? 
                  AND end_date >= ?
                LIMIT 1";

        $result = $this->db->connection()->selectOne($sql, [$employeeId, $endDate, $startDate]);

        return $result !== null;
    }
}