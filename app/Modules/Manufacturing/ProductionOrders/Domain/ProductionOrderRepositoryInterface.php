<?php
// Path: app/Modules/Manufacturing/ProductionOrders/Domain/ProductionOrderRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\ProductionOrders\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Production Order
 */
interface ProductionOrderRepositoryInterface extends RepositoryInterface
{
    /**
     * Generate a sequential Production Order number.
     *
     * @param int $companyId
     * @return string
     */
    public function generateOrderNumber(int $companyId): string;

    /**
     * Save the Production Order and its calculated material requirements atomically.
     *
     * @param array $orderData
     * @param array $items
     * @return int
     */
    public function saveWithItems(array $orderData, array $items): int;

    /**
     * Mark the order as completed.
     *
     * @param int $orderId
     * @return void
     */
    public function markAsCompleted(int $orderId): void;
}