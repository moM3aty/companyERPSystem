<?php
// Path: app/Modules/Purchasing/Returns/Infrastructure/DebitNoteRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Returns\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\Returns\Domain\DebitNoteRepositoryInterface;

class DebitNoteRepository extends BaseRepository implements DebitNoteRepositoryInterface
{
    protected string $table = 'purchasing_debit_notes';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateDebitNoteNumber(int $companyId): string
    {
        $prefix = 'DN-' . date('ym') . '-';
        
        $lastNote = $this->newQuery()
            ->select(['debit_note_no'])
            ->where('company_id', '=', $companyId)
            ->where('debit_note_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastNote) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastNote['debit_note_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $debitNoteId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $debitNoteId,
                $item['product_id'],
                $item['warehouse_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['tax_amount'],
                $item['total']
            );
        }

        $sql = "INSERT INTO purchasing_debit_note_items 
                (debit_note_id, product_id, warehouse_id, quantity, unit_price, tax_amount, total) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}