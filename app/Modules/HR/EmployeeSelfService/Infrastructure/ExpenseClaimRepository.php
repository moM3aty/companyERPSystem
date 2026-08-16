<?php
// Path: app/Modules/HR/EmployeeSelfService/Infrastructure/ExpenseClaimRepository.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\HR\EmployeeSelfService\Domain\ExpenseClaimRepositoryInterface;

class ExpenseClaimRepository extends BaseRepository implements ExpenseClaimRepositoryInterface
{
    protected string $table = 'hr_expense_claims';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateClaimNumber(int $companyId): string
    {
        $prefix = 'EXP-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['claim_no'])
            ->where('company_id', '=', $companyId)
            ->where('claim_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['claim_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $claimId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $claimId,
                $item['expense_type'],
                $item['receipt_date'],
                $item['amount'],
                $item['description'],
                $item['attachment_path'] ?? null
            );
        }

        $sql = "INSERT INTO hr_expense_claim_items 
                (expense_claim_id, expense_type, receipt_date, amount, description, attachment_path) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}