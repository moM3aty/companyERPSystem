<?php
// Path: app/Modules/Inventory/Products/Domain/ProductRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\Products\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Product
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن صنف بواسطة الكود (SKU).
     *
     * @param string $code
     * @param int $companyId
     * @return Product|null
     */
    public function findByCode(string $code, int $companyId): ?Product;

    /**
     * البحث عن صنف بواسطة الباركود (مهم جداً لنقاط البيع POS).
     *
     * @param string $barcode
     * @param int $companyId
     * @return Product|null
     */
    public function findByBarcode(string $barcode, int $companyId): ?Product;
}