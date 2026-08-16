<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/BankAccountModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class BankAccountModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function fetchAll(int $companyId): array
    {
        // Assuming table `bank_accounts` exists
        $stmt = $this->db->prepare("SELECT * FROM bank_accounts WHERE company_id = :cid AND is_active = 1");
        $stmt->execute([':cid' => $companyId]);
        return $stmt->fetchAll();
    }

    public function updateBalance(int $id, float $amount, int $companyId): bool
    {
        $stmt = $this->db->prepare("UPDATE bank_accounts SET current_balance = current_balance + :amount WHERE id = :id AND company_id = :cid");
        return $stmt->execute([':amount' => $amount, ':id' => $id, ':cid' => $companyId]);
    }
}