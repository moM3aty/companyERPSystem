<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/JournalEntryLineModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class JournalEntryLineModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function insertLine(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO journal_entry_lines (journal_entry_id, account_id, cost_center_id, debit, credit, description)
            VALUES (:journal_entry_id, :account_id, :cost_center_id, :debit, :credit, :description)
        ");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function fetchLinesByEntryId(int $entryId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM journal_entry_lines WHERE journal_entry_id = :id ORDER BY id ASC");
        $stmt->execute([':id' => $entryId]);
        return $stmt->fetchAll();
    }
}