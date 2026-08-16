<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/AccountModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class AccountModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function fetchAll(int $companyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM chart_of_accounts WHERE company_id = :cid AND deleted_at IS NULL ORDER BY account_code ASC");
        $stmt->execute([':cid' => $companyId]);
        return $stmt->fetchAll();
    }

    public function fetchById(int $id, int $companyId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM chart_of_accounts WHERE id = :id AND company_id = :cid AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([':id' => $id, ':cid' => $companyId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchByCode(string $code, int $companyId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM chart_of_accounts WHERE account_code = :code AND company_id = :cid AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([':code' => $code, ':cid' => $companyId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO chart_of_accounts (company_id, parent_id, account_code, account_name, account_type, normal_balance, level, is_control_account, is_active)
            VALUES (:company_id, :parent_id, :account_code, :account_name, :account_type, :normal_balance, :level, :is_control_account, :is_active)
        ");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, int $companyId, int $isActive): bool
    {
        $stmt = $this->db->prepare("UPDATE chart_of_accounts SET is_active = :status WHERE id = :id AND company_id = :cid");
        return $stmt->execute([':status' => $isActive, ':id' => $id, ':cid' => $companyId]);
    }
}