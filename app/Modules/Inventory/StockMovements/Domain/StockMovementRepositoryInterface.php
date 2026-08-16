<?php
// Path: app/Modules/Inventory/StockMovements/Domain/StockMovementRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockMovements\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Stock Movement
 */
interface StockMovementRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب حركات صنف معين في مستودع معين (Item Ledger).
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $companyId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getByProductAndWarehouse(int $productId, int $warehouseId, int $companyId, int $limit = 50, int $offset = 0): array;
    
    /**
     * جلب الحركات المرتبطة بمستند معين (مثال: حركات إذن التسليم رقم 10).
     *
     * @param string $referenceType
     * @param int $referenceId
     * @param int $companyId
     * @return array
     */
    public function getByReference(string $referenceType, int $referenceId, int $companyId): array;
}