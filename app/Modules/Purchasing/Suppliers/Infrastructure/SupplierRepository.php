<?php
// Path: app/Modules/Purchasing/Suppliers/Infrastructure/SupplierRepository.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Purchasing\Suppliers\Domain\SupplierRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Supplier
 */
class SupplierRepository extends BaseRepository implements SupplierRepositoryInterface
{
    protected string $table = 'suppliers';
    protected bool $useTenantScope = true;
    protected bool $useSoftDeletes = true;

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function findByCode(string $supplierCode, int $companyId): ?array
    {
        $result = $this->newQuery()
                       ->where('supplier_code', '=', $supplierCode)
                       ->where('company_id', '=', $companyId)
                       ->first();

        return $result ?: null;
    }
}