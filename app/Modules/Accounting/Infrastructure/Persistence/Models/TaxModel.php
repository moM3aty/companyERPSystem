<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/TaxModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class TaxModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function fetchAllActive(int $companyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM taxes WHERE company_id = :cid AND is_active = 1");
        $stmt->execute([':cid' => $companyId]);
        return $stmt->fetchAll();
    }
}