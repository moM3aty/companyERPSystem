<?php
// Path: app/Modules/Inventory/Warehouses/Infrastructure/WarehouseRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Warehouses\Domain\Warehouse;
use App\Modules\Inventory\Warehouses\Domain\WarehouseRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Warehouse
 */
class WarehouseRepository extends BaseRepository implements WarehouseRepositoryInterface
{
    protected string $table = 'warehouses';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByCode(string $code, int $companyId): ?Warehouse
    {
        $data = $this->newQuery()
                     ->where('code', '=', $code)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new Warehouse($data) : null;
    }
}