<?php
// Path: app/Modules/Inventory/Warehouses/Domain/WarehouseRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\Warehouses\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Warehouse
 */
interface WarehouseRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن مستودع באמצעות كوده الخاص داخل الشركة.
     *
     * @param string $code
     * @param int $companyId
     * @return Warehouse|null
     */
    public function findByCode(string $code, int $companyId): ?Warehouse;
}