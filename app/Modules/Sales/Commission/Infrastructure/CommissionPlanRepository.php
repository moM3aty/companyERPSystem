<?php
// Path: app/Modules/Sales/Commission/Infrastructure/CommissionPlanRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\Commission\Domain\CommissionPlanRepositoryInterface;

class CommissionPlanRepository extends BaseRepository implements CommissionPlanRepositoryInterface
{
    protected string $table = 'sales_commission_plans';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function getActivePlanForEmployee(int $employeeId, int $companyId): ?array
    {
        // بافتراض وجود جدول ربط (employee_commission_plans)
        $sql = "SELECT p.* FROM sales_commission_plans p
                JOIN employee_commission_plans ecp ON p.id = ecp.commission_plan_id
                WHERE ecp.employee_id = ? AND p.company_id = ? AND p.is_active = 1 LIMIT 1";
                
        $result = $this->db->connection()->selectOne($sql, [$employeeId, $companyId]);

        return $result ?: null;
    }
}