<?php
// Path: app/Modules/Inventory/StockTaking/Infrastructure/StockCountRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\StockTaking\Domain\StockCountRepositoryInterface;

class StockCountRepository extends BaseRepository implements StockCountRepositoryInterface
{
    protected string $table = 'inventory_stock_counts';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function generateCountNumber(int $companyId): string
    {
        $prefix = 'STK-' . date('ym') . '-';
        
        $lastRecord = $this->newQuery()
            ->select(['count_number'])
            ->where('company_id', '=', $companyId)
            ->where('count_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastRecord) {
            return $prefix . '0001';
        }

        $lastNumber = (int) str_replace($prefix, '', $lastRecord['count_number']);
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function bulkInsertItems(int $stockCountId, array $items): void
    {
        if (empty($items)) return;

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?)";

        foreach ($items as $item) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $stockCountId,
                $item['product_id'],
                $item['system_quantity'],
                $item['counted_quantity'],
                $item['difference'],
                $item['unit_cost']
            );
        }

        $sql = "INSERT INTO inventory_stock_count_items 
                (stock_count_id, product_id, system_quantity, counted_quantity, difference, unit_cost) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}