<?php
// Path: app/Modules/Inventory/Products/Infrastructure/ProductRepository.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Inventory\Products\Domain\Product;
use App\Modules\Inventory\Products\Domain\ProductRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Product
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected string $table = 'products';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByCode(string $code, int $companyId): ?Product
    {
        $data = $this->newQuery()
                     ->where('code', '=', $code)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new Product($data) : null;
    }

    /**
     * @inheritDoc
     */
    public function findByBarcode(string $barcode, int $companyId): ?Product
    {
        $data = $this->newQuery()
                     ->where('barcode', '=', $barcode)
                     ->where('company_id', '=', $companyId)
                     ->first();

        return $data ? new Product($data) : null;
    }
}