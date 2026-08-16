<?php
// Path: app/Modules/Inventory/StockTaking/Domain/StockCountRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\StockTaking\Domain;

use App\Core\Contracts\RepositoryInterface;

interface StockCountRepositoryInterface extends RepositoryInterface
{
    public function generateCountNumber(int $companyId): string;
    public function bulkInsertItems(int $stockCountId, array $items): void;
}