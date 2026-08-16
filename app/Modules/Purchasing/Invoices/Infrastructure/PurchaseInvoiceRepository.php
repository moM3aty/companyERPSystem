<?php
// Path: app/Modules/Purchasing/Invoices/Infrastructure/PurchaseInvoiceRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\Invoices\Domain\PurchaseInvoiceRepositoryInterface;

class PurchaseInvoiceRepository extends BaseRepository implements PurchaseInvoiceRepositoryInterface
{
    protected string $table = 'purchase_invoices';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; 

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateInvoiceNumber(int $companyId): string
    {
        $prefix = 'BILL-' . date('ym') . '-';
        
        $lastInvoice = $this->newQuery()
            ->select(['invoice_no'])
            ->where('company_id', '=', $companyId)
            ->where('invoice_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastInvoice['invoice_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $invoiceId, array $items): void
    {
        if (empty($items)) return;

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

        $sql = "INSERT INTO purchase_invoice_items 
                (purchase_invoice_id, product_id, description, quantity, unit_price, discount_amount, tax_amount, total, warehouse_id) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}