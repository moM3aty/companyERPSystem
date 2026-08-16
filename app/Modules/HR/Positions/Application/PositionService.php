<?php
// Path: app/Modules/HR/Positions/Application/PositionService.php

declare(strict_types=1);

namespace App\Modules\HR\Positions\Application;

use App\Core\Database\DatabaseManager;

class PositionService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function createPosition(array $data, int $companyId): int
    {
        $data['company_id'] = $companyId;
        $data['is_active']  = $data['is_active'] ?? 1;
        $data['created_at'] = date('Y-m-d H:i:s');

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $this->db->connection()->insert(
            "INSERT INTO hr_positions ({$columns}) VALUES ({$placeholders})",
            array_values($data)
        );

        return (int) $this->db->connection()->lastInsertId();
    }
}