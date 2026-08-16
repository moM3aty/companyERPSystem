<?php
// Path: app/Modules/AdvancedHR/Services/PerformanceService.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;

class PerformanceService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function submitReview(array $data, int $companyId): int
    {
        $cycle = $this->db->connection()->selectOne(
            "SELECT id FROM advanced_hr_performance_cycles WHERE id = ? AND company_id = ? AND is_active = 1",
            [$data['performance_cycle_id'], $companyId]
        );

        if (!$cycle) {
            throw new BusinessException("Invalid or inactive performance cycle.");
        }

        $score = (float)$data['overall_score'];
        $grade = 'Needs Improvement';
        if ($score >= 90) $grade = 'Excellent';
        elseif ($score >= 75) $grade = 'Good';
        elseif ($score >= 60) $grade = 'Satisfactory';

        $this->db->connection()->insert(
            "INSERT INTO advanced_hr_performance_reviews 
            (company_id, performance_cycle_id, employee_id, reviewer_id, overall_score, rating_grade, comments, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'submitted', ?)",
            [
                $companyId, 
                $data['performance_cycle_id'], 
                $data['employee_id'], 
                $data['reviewer_id'], 
                $score, 
                $grade, 
                $data['comments'] ?? '', 
                date('Y-m-d H:i:s')
            ]
        );

        return (int) $this->db->connection()->getPdo()->lastInsertId();
    }
}