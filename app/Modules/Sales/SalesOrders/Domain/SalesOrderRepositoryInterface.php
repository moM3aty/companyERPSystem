<?php
// Path: app/Modules/Sales/SalesOrders/Domain/SalesOrderRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\SalesOrders\Domain;

use App\Core\Contracts\RepositoryInterface;

interface SalesOrderRepositoryInterface extends RepositoryInterface
{
    public function generateOrderNumber(int $companyId): string;
    public function bulkInsertItems(int $orderId, array $items): void;
}