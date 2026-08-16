<?php
// Path: app/Modules/Inventory/Stock/Domain/StockRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\Stock\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Stock
 */
interface StockRepositoryInterface extends RepositoryInterface
{
    /**
     * البحث عن رصيد صنف داخل مستودع.
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $companyId
     * @return Stock|null
     */
    public function findByProductAndWarehouse(int $productId, int $warehouseId, int $companyId): ?Stock;
    
    /**
     * جلب الرصيد وتطبيق (Pessimistic Lock) لمنع التعديل المتزامن (Race Conditions).
     *
     * @param int $productId
     * @param int $warehouseId
     * @param int $companyId
     * @return Stock|null
     */
    public function lockForUpdate(int $productId, int $warehouseId, int $companyId): ?Stock;
    
    /**
     * جلب أرصدة صنف معين في جميع المستودعات.
     *
     * @param int $productId
     * @param int $companyId
     * @return array
     */
    public function getStockByProduct(int $productId, int $companyId): array;
}