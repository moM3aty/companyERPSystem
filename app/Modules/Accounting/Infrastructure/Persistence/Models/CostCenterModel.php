<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/CostCenterModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class CostCenterModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function fetchAll(int $companyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM cost_centers WHERE company_id = :cid AND is_active = 1 ORDER BY code ASC");
        $stmt->execute([':cid' => $companyId]);
        return $stmt->fetchAll();
    }
}