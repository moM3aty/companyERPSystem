<?php
// Path: app/Modules/Inventory/Stock/Infrastructure/StockRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\Stock\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Stock\Domain\Stock;
use App\Modules\Inventory\Stock\Domain\StockRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Stock
 */
class StockRepository extends BaseRepository implements StockRepositoryInterface
{
    protected string $table = 'inventory_stocks';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Stocks are real-time numbers, never soft deleted

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByProductAndWarehouse(int $productId, int $warehouseId, int $companyId): ?Stock
    {
        $data = $this->newQuery()
            ->where('product_id', '=', $productId)
            ->where('warehouse_id', '=', $warehouseId)
            ->where('company_id', '=', $companyId)
            ->first();

        return $data ? new Stock($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function lockForUpdate(int $productId, int $warehouseId, int $companyId): ?Stock
    {
        // استخدام Raw Query صريح لتطبيق FOR UPDATE للحماية من الـ Race Conditions
        $sql = "SELECT * FROM {$this->table} WHERE product_id = ? AND warehouse_id = ? AND company_id = ? FOR UPDATE";
        
        $data = $this->db->connection()->selectOne($sql, [$productId, $warehouseId, $companyId]);

        return $data ? new Stock($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function getStockByProduct(int $productId, int $companyId): array
    {
        $records = $this->newQuery()
            ->where('product_id', '=', $productId)
            ->where('company_id', '=', $companyId)
            ->get();

        return array_map(fn($record) => new Stock($record), $records);
    }
}