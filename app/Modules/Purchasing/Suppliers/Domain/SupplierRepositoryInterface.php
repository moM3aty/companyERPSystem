<?php
// Path: app/Modules/Purchasing/Suppliers/Domain/SupplierRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Suppliers\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Supplier
 */
interface SupplierRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن مورد باستخدام كوده الخاص داخل الشركة.
     *
     * @param string $supplierCode
     * @param int $companyId
     * @return array|null
     */
    public function findByCode(string $supplierCode, int $companyId): ?array;
}