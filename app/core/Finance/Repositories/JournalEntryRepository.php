<?php
// Path: app/Core/Finance/Repositories/JournalEntryRepository.php

declare(strict_types=1);

namespace App\Core\Finance\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Journal Entry Repository
 * Handles the creation of strict double-entry accounting records.
 */
class JournalEntryRepository extends BaseRepository
{
    protected string $table = 'journal_entries';
    protected bool $useTenantScope = true;

    /**
     * JournalEntryRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Generates a unique sequential Journal Entry number.
     * Format: JV-YYYYMM-XXXX (e.g., JV-202608-0001)
     *
     * @param int $companyId
     * @return string
     */
    public function generateEntryNumber(int $companyId): string
    {
        $prefix = 'JV-' . date('Ym') . '-';
        
        $lastEntry = $this->newQuery()
            ->select(['entry_no'])
            ->where('company_id', '=', $companyId)
            ->where('entry_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastEntry) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastEntry['entry_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Bulk insert multiple lines for a Journal Entry.
     * Highly optimized for performance.
     *
     * @param int $journalEntryId
     * @param array $lines Array of debits and credits
     * @return void
     */
    public function bulkInsertLines(int $journalEntryId, array $lines): void
    {
        if (empty($lines)) {
            return;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($lines as $line) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $journalEntryId,
                $line['account_id'],
                $line['cost_center_id'] ?? null,
                $line['department_id'] ?? null,
                $line['project_id'] ?? null,
                $line['debit'] ?? 0.00,
                $line['credit'] ?? 0.00,
                $line['description'] ?? null
            );
        }

        $sql = "INSERT INTO journal_entry_lines 
                (journal_entry_id, account_id, cost_center_id, department_id, project_id, debit, credit, description) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}