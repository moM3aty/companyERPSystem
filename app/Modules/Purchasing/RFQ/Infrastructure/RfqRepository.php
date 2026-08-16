<?php
// Path: app/Modules/Purchasing/RFQ/Infrastructure/RfqRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\RFQ\Domain\RfqRepositoryInterface;

class RfqRepository extends BaseRepository implements RfqRepositoryInterface
{
    protected string $table = 'purchasing_rfqs';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateRfqNumber(int $companyId): string
    {
        $prefix = 'RFQ-' . date('ym') . '-';
        
        $lastRecord = $this->newQuery()
            ->select(['rfq_number'])
            ->where('company_id', '=', $companyId)
            ->where('rfq_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRecord) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRecord['rfq_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $rfqId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push($bindings, $rfqId, $item['product_id'], $item['description'] ?? null, $item['quantity']);
        }

        $sql = "INSERT INTO purchasing_rfq_items (rfq_id, product_id, description, quantity) VALUES " . implode(', ', $values);
        $this->db->connection()->insert($sql, $bindings);
    }

    public function bulkInsertSuppliers(int $rfqId, array $supplierIds): void
    {
        if (empty($supplierIds)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, 0, 0)";

        foreach ($supplierIds as $supplierId) {
            $values[] = $placeholders;
            array_push($bindings, $rfqId, $supplierId);
        }

        $sql = "INSERT INTO purchasing_rfq_suppliers (rfq_id, supplier_id, has_bid, is_winner) VALUES " . implode(', ', $values);
        $this->db->connection()->insert($sql, $bindings);
    }
}