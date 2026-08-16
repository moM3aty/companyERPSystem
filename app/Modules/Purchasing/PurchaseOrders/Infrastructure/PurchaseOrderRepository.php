<?php
// Path: app/Modules/Purchasing/PurchaseOrders/Infrastructure/PurchaseOrderRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\PurchaseOrders\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\PurchaseOrders\Domain\PurchaseOrderRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Purchase Order
 */
class PurchaseOrderRepository extends BaseRepository implements PurchaseOrderRepositoryInterface
{
    protected string $table = 'purchase_orders';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Financial documents are cancelled, not deleted.

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function generatePoNumber(int $companyId): string
    {
        $prefix = 'PO-' . date('ym') . '-';
        
        $lastPo = $this->newQuery()
            ->select(['po_number'])
            ->where('company_id', '=', $companyId)
            ->where('po_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPo) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastPo['po_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @inheritDoc
     */
    public function bulkInsertItems(int $poId, array $items): void
    {
        if (empty($items)) {
            return;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $poId,
                $item['product_id'],
                $item['description'] ?? null,
                $item['quantity'],
                $item['unit_price'],
                $item['discount_amount'] ?? 0.00,
                $item['tax_amount'] ?? 0.00,
                $item['total']
            );
        }

        $sql = "INSERT INTO purchase_order_items 
                (purchase_order_id, product_id, description, quantity, unit_price, discount_amount, tax_amount, total) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}