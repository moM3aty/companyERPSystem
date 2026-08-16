<?php
// Path: app/Modules/Treasury/Reconciliation/Infrastructure/BankStatementRepository.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Treasury\Reconciliation\Domain\BankStatementRepositoryInterface;

class BankStatementRepository extends BaseRepository implements BankStatementRepositoryInterface
{
    protected string $table = 'treasury_bank_statements';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function bulkInsertLines(int $statementId, array $lines): void
    {
        if (empty($lines)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, 0)";

        foreach ($lines as $line) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $statementId,
                $line['transaction_date'],
                $line['description'],
                $line['reference'] ?? null,
                $line['amount']
            );
        }

        $sql = "INSERT INTO treasury_bank_statement_lines 
                (bank_statement_id, transaction_date, description, reference, amount, is_matched) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }

    public function getUnmatchedLines(int $statementId): array
    {
        return $this->db->connection()->select(
            "SELECT * FROM treasury_bank_statement_lines WHERE bank_statement_id = ? AND is_matched = 0",
            [$statementId]
        );
    }
}