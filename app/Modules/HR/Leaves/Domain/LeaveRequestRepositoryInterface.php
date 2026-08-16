<?php
// Path: app/Modules/HR/Leaves/Domain/LeaveRequestRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\HR\Leaves\Domain;

use App\Core\Contracts\RepositoryInterface;

interface LeaveRequestRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان هناك تداخل مع إجازات سابقة لنفس الموظف.
     *
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @return bool
     */
    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate): bool;
}