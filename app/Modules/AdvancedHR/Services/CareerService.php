<?php
// Path: app/Modules/AdvancedHR/Services/CareerService.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class CareerService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function createCareerPlan(array $data, int $companyId): int
    {
        $exists = $this->db->connection()->selectOne(
            "SELECT id FROM advanced_hr_career_plans WHERE employee_id = ? AND target_position_id = ? AND status = 'active'",
            [$data['employee_id'], $data['target_position_id']]
        );

        if ($exists) {
            throw new BusinessException("An active career plan for this target position already exists for the employee.");
        }

        $this->db->connection()->insert(
            "INSERT INTO advanced_hr_career_plans (company_id, employee_id, current_position_id, target_position_id, readiness_timeframe, status, created_at) 
             VALUES (?, ?, ?, ?, ?, 'active', ?)",
            [
                $companyId, 
                $data['employee_id'], 
                $data['current_position_id'], 
                $data['target_position_id'], 
                $data['readiness_timeframe'], 
                date('Y-m-d H:i:s')
            ]
        );

        return (int) $this->db->connection()->getPdo()->lastInsertId();
    }
}