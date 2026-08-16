<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Infrastructure/ProductionOrderRepository.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Manufacturing\ProductionOrders\Domain\ProductionOrderRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Production Order
 */
class ProductionOrderRepository extends BaseRepository implements ProductionOrderRepositoryInterface
{
    protected string $table = 'manufacturing_production_orders';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function generateOrderNumber(int $companyId): string
    {
        $prefix = 'PROD-' . date('ym') . '-';
        
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

    /**
     * @inheritDoc
     */
    public function saveWithItems(array $orderData, array $items): int
    {
        $orderId = $this->create($orderData);

        if (empty($items)) {
            return $orderId;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $orderId,
                $item['component_product_id'],
                $item['required_quantity'],
                0.00 // consumed_quantity is initially 0 until actual issuance occurs
            );
        }

        $sql = "INSERT INTO manufacturing_production_order_items 
                (production_order_id, component_product_id, required_quantity, consumed_quantity) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);

        return $orderId;
    }

    /**
     * @inheritDoc
     */
    public function markAsCompleted(int $orderId): void
    {
        $this->update($orderId, [
            'status'     => 'completed',
            'end_date'   => date('Y-m-d'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}