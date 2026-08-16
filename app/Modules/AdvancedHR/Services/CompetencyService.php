<?php
// Path: app/Modules/AdvancedHR/Services/CompetencyService.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;

/**
 * Enterprise Application Service: Competency Management
 * محرك الكفاءات (Skills Engine). يدير الجدارات وتقييم الموظفين لتجهيزهم للترقيات.
 */
class CompetencyService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    /**
     * تقييم أو تحديث مهارة/كفاءة لموظف.
     *
     * @param int $employeeId
     * @param int $competencyId
     * @param int $proficiencyLevel (1-5)
     * @param int $assessorId
     * @return void
     */
    public function assessEmployee(int $employeeId, int $competencyId, int $proficiencyLevel, int $assessorId): void
    {
        $this->transaction->execute(function () use ($employeeId, $competencyId, $proficiencyLevel, $assessorId) {
            
            $now = date('Y-m-d H:i:s');

            $existing = $this->db->connection()->selectOne(
                "SELECT id FROM advanced_hr_employee_competencies WHERE employee_id = ? AND competency_id = ?",
                [$employeeId, $competencyId]
            );

            if ($existing) {
                $this->db->connection()->update(
                    "UPDATE advanced_hr_employee_competencies SET proficiency_level = ?, assessor_id = ?, last_assessed_at = ? WHERE id = ?",
                    [$proficiencyLevel, $assessorId, $now, $existing['id']]
                );
            } else {
                $this->db->connection()->insert(
                    "INSERT INTO advanced_hr_employee_competencies (employee_id, competency_id, proficiency_level, assessor_id, last_assessed_at) VALUES (?, ?, ?, ?, ?)",
                    [$employeeId, $competencyId, $proficiencyLevel, $assessorId, $now]
                );
            }
        });
    }
}