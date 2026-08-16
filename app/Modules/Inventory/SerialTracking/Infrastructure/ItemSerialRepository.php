<?php
// Path: app/Modules/Inventory/SerialTracking/Infrastructure/ItemSerialRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\SerialTracking\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\SerialTracking\Domain\ItemSerialRepositoryInterface;

class ItemSerialRepository extends BaseRepository implements ItemSerialRepositoryInterface
{
    protected string $table = 'inventory_serials';
    protected bool $useTenantScope = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    public function existsForProduct(string $serialNumber, int $productId, int $companyId): bool
    {
        $result = $this->newQuery()
            ->where('serial_number', '=', $serialNumber)
            ->where('product_id', '=', $productId)
            ->where('company_id', '=', $companyId)
            ->first();

        return $result !== null;
    }
}