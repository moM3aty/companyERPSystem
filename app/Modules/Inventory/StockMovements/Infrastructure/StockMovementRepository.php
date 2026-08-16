<?php
// Path: app/Modules/Inventory/StockMovements/Infrastructure/StockMovementRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\StockMovements\Domain\StockMovement;
use App\Modules\Inventory\StockMovements\Domain\StockMovementRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Stock Movement
 */
class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    protected string $table = 'inventory_stock_movements';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = false; // Movements are strictly immutable

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function getByProductAndWarehouse(int $productId, int $warehouseId, int $companyId, int $limit = 50, int $offset = 0): array
    {
        $records = $this->newQuery()
            ->where('product_id', '=', $productId)
            ->where('warehouse_id', '=', $warehouseId)
            ->where('company_id', '=', $companyId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return array_map(fn($record) => new StockMovement($record), $records);
    }

    /**
     * @inheritDoc
     */
    public function getByReference(string $referenceType, int $referenceId, int $companyId): array
    {
        $records = $this->newQuery()
            ->where('reference_type', '=', $referenceType)
            ->where('reference_id', '=', $referenceId)
            ->where('company_id', '=', $companyId)
            ->orderBy('id', 'asc')
            ->get();

        return array_map(fn($record) => new StockMovement($record), $records);
    }
}