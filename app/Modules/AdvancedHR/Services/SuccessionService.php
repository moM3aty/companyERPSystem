<?php
// Path: app/Modules/AdvancedHR/Services/SuccessionService.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Services;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Application Service: Succession Planning
 * محرك التعاقب الوظيفي. يفحص المناصب الحرجة ويقيم مدى جاهزية الخلفاء.
 */
class SuccessionService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل خطة تعاقب وظيفي.
     *
     * @param array $data
     * @param int $companyId
     * @return int
     */
    public function createPlan(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        $data['status']     = 'active';
        $data['created_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO advanced_hr_succession_plans ({$columns}) VALUES ({$placeholders})";
        $this->db->connection()->insert($sql, array_values($data));
        
        return (int) $this->db->connection()->lastInsertId();
    }
}