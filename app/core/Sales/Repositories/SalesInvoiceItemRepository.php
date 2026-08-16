<?php
// Path: app/Core/Sales/Repositories/SalesInvoiceItemRepository.php

declare(strict_types=1);

namespace App\Core\Sales\Repositories;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise Sales Invoice Item Repository
 * Manages Invoice Lines (Items).
 * Does not use TenantScope directly as it relies on the parent Invoice ownership.
 */
class SalesInvoiceItemRepository extends BaseRepository
{
    protected string $table = 'sales_invoice_items';
    protected bool $useTenantScope = false; // Scoped via invoice_id

    /**
     * SalesInvoiceItemRepository constructor.
     *
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * Bulk insert multiple items for an invoice to maximize performance.
     *
     * @param int $invoiceId
     * @param array $items Array of items (product_id, quantity, unit_price, etc.)
     * @return void
     */
    public function bulkInsert(int $invoiceId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $invoiceId,
                $item['product_id'],
                $item['description'] ?? null,
                $item['quantity'],
                $item['unit_price'],
                $item['discount_amount'] ?? 0.00,
                $item['tax_amount'] ?? 0.00,
                $item['total'],
                $item['warehouse_id'] ?? null
            );
        }

        $sql = "INSERT INTO {$this->table} 
                (invoice_id, product_id, description, quantity, unit_price, discount_amount, tax_amount, total, warehouse_id) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }

    /**
     * Fetch all items belonging to a specific invoice.
     *
     * @param int $invoiceId
     * @return array
     */
    public function getByInvoiceId(int $invoiceId): array
    {
        return $this->newQuery()
                    ->where('invoice_id', '=', $invoiceId)
                    ->get();
    }
}