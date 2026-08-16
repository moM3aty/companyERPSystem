<?php
// Path: app/Modules/Manufacturing/BOM/Domain/BOMRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\BOM\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: BOM
 */
interface BOMRepositoryInterface extends RepositoryInterface
{
    /**
     * Find the active BOM for a specific finished product.
     *
     * @param int $productId
     * @param int $companyId
     * @return array|null
     */
    public function getActiveBOMForProduct(int $productId, int $companyId): ?array;

    /**
     * Save the BOM header and its component items atomically.
     *
     * @param array $bomData
     * @param array $items
     * @return int The ID of the newly created BOM
     */
    public function saveWithItems(array $bomData, array $items): int;
}