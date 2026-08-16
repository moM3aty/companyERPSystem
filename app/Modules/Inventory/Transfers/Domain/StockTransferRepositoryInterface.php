<?php
// Path: app/Modules/Inventory/Transfers/Domain/StockTransferRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Inventory\Transfers\Domain;

use App\Core\Contracts\RepositoryInterface;

interface StockTransferRepositoryInterface extends RepositoryInterface
{
    public function generateTransferNumber(int $companyId): string;
    public function bulkInsertItems(int $transferId, array $items): void;
}