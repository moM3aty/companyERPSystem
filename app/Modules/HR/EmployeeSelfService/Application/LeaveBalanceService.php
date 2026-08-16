<?php
// Path: app/Modules/HR/EmployeeSelfService/Application/LeaveBalanceService.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: ESS Leave Balance
 * يحسب رصيد الإجازات المتبقي للموظف بناءً على الاستحقاق السنوي والإجازات المأخوذة.
 */
class LeaveBalanceService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * جلب رصيد الإجازات المتاح للموظف.
     *
     * @param int $employeeId
     * @param int $companyId
     * @param int $year
     * @return array
     * @throws BusinessException
     */
    public function getAnnualLeaveBalance(int $employeeId, int $companyId, int $year): array
    {
        // 1. جلب استحقاق الموظف من عقده المباشر أو من إعدادات الشركة
        $contract = $this->db->connection()->selectOne(
            "SELECT annual_leave_days FROM hr_contracts WHERE employee_id = ? AND company_id = ? AND status = 'active' LIMIT 1",
            [$employeeId, $companyId]
        );

        $totalEntitled = $contract ? (int) $contract['annual_leave_days'] : 21; // 21 يوم كحد أدنى لقانون العمل

        // 2. جلب عدد الأيام المستنفدة (مقبولة ومعتمدة)
        $usedLeaves = $this->db->connection()->selectOne(
            "SELECT SUM(total_days) as used_days FROM hr_leave_requests 
             WHERE employee_id = ? AND leave_type = 'annual' AND status IN ('approved', 'completed') AND YEAR(start_date) = ?",
            [$employeeId, $year]
        );

        $usedDays = (int) ($usedLeaves['used_days'] ?? 0);
        $remaining = max(0, $totalEntitled - $usedDays);

        return [
            'year'           => $year,
            'total_entitled' => $totalEntitled,
            'used_days'      => $usedDays,
            'remaining_days' => $remaining,
        ];
    }
}