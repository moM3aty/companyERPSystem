<?php
// Path: app/Modules/Accounting/Infrastructure/Persistence/Models/JournalEntryModel.php

declare(strict_types=1);

namespace App\Modules\Accounting\Infrastructure\Persistence\Models;

use App\Core\Database\DatabaseManager;
use PDO;

class JournalEntryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DatabaseManager::getConnection();
    }

    public function fetchAllWithTotals(int $companyId): array
    {
        $stmt = $this->db->prepare("
            SELECT je.*, COALESCE((SELECT SUM(debit) FROM journal_entry_lines WHERE journal_entry_id = je.id), 0) as total_amount
            FROM journal_entries je 
            WHERE je.company_id = :cid 
            ORDER BY je.entry_date DESC, je.id DESC
        ");
        $stmt->execute([':cid' => $companyId]);
        return $stmt->fetchAll();
    }

    public function insertHeader(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO journal_entries (company_id, entry_no, entry_date, reference_type, reference_id, description, status, created_by)
            VALUES (:company_id, :entry_no, :entry_date, :reference_type, :reference_id, :description, :status, :created_by)
        ");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function updateStatus(int $id, int $companyId, string $status, int $userId): bool
    {
        $stmt = $this->db->prepare("UPDATE journal_entries SET status = :status, posted_by = :uid, posted_at = NOW() WHERE id = :id AND company_id = :cid");
        return $stmt->execute([':status' => $status, ':uid' => $userId, ':id' => $id, ':cid' => $companyId]);
    }
}