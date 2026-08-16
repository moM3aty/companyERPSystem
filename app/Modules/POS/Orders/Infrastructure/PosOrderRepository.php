<?php
// Path: app/Modules/POS/Orders/Infrastructure/PosOrderRepository.php

declare(strict_types=1);

namespace App\Modules\POS\Orders\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\POS\Orders\Domain\PosOrderRepositoryInterface;

class PosOrderRepository extends BaseRepository implements PosOrderRepositoryInterface
{
    protected string $table = 'pos_orders';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; 

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateOrderNumber(int $companyId): string
    {
        $prefix = 'POS-' . date('ymd') . '-';
        
        $lastOrder = $this->newQuery()
            ->select(['order_number'])
            ->where('company_id', '=', $companyId)
            ->where('order_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastOrder['order_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $orderId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $orderId,
                $item['product_id'],
                $item['quantity'],
                $item['unit_price'],
                $item['tax_amount'],
                $item['discount_amount'],
                $item['total']
            );
        }

        $sql = "INSERT INTO pos_order_items 
                (order_id, product_id, quantity, unit_price, tax_amount, discount_amount, total) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}