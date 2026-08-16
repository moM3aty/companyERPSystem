<?php
// Path: app/Modules/Sales/SalesOrders/Infrastructure/SalesOrderRepository.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Sales\SalesOrders\Domain\SalesOrderRepositoryInterface;

class SalesOrderRepository extends BaseRepository implements SalesOrderRepositoryInterface
{
    protected string $table = 'sales_orders';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateOrderNumber(int $companyId): string
    {
        $prefix = 'SO-' . date('ym') . '-';
        
        $lastRow = $this->newQuery()
            ->select(['order_no'])
            ->where('company_id', '=', $companyId)
            ->where('order_no', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRow) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRow['order_no']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $orderId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $orderId,
                $item['product_id'],
                $item['description'] ?? null,
                $item['quantity'],
                0.0, // delivered_quantity
                0.0, // invoiced_quantity
                $item['unit_price'],
                $item['discount_amount'] ?? 0.0,
                $item['tax_amount'] ?? 0.0,
                $item['total']
            );
        }

        $sql = "INSERT INTO sales_order_items 
                (sales_order_id, product_id, description, quantity, delivered_quantity, invoiced_quantity, unit_price, discount_amount, tax_amount, total) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}