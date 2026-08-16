<?php
// Path: app/Modules/Inventory/LandedCost/Infrastructure/LandedCostRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\LandedCost\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\LandedCost\Domain\LandedCostRepositoryInterface;

class LandedCostRepository extends BaseRepository implements LandedCostRepositoryInterface
{
    protected string $table = 'inventory_landed_costs';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function bulkInsertAllocations(int $landedCostId, array $allocations): void
    {
        if (empty($allocations)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?)";

        foreach ($allocations as $alloc) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $landedCostId,
                $alloc['goods_receipt_item_id'],
                $alloc['product_id'],
                $alloc['allocated_amount']
            );
        }

        $sql = "INSERT INTO inventory_landed_cost_allocations 
                (landed_cost_id, goods_receipt_item_id, product_id, allocated_amount) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}