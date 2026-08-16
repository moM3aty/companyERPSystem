<?php
// Path: app/Modules/Sales/CreditNotes/Infrastructure/CreditNoteRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\CreditNotes\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\CreditNotes\Domain\CreditNoteRepositoryInterface;

class CreditNoteRepository extends BaseRepository implements CreditNoteRepositoryInterface
{
    protected string $table = 'sales_credit_notes';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateCreditNoteNumber(int $companyId): string
    {
        $prefix = 'CN-' . date('ym') . '-';
        
        $lastNote = $this->newQuery()
            ->select(['credit_note_no'])
            ->where('company_id', '=', $companyId)
            ->where('credit_note_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastNote) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastNote['credit_note_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $creditNoteId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $creditNoteId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['tax_amount'],
                $item['total'],
                $item['warehouse_id'] ?? null
            );
        }

        $sql = "INSERT INTO sales_credit_note_items 
                (credit_note_id, product_id, quantity, unit_price, tax_amount, total, warehouse_id) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}