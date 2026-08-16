<?php
// Path: app/Modules/Purchasing/GoodsReceipts/Domain/GoodsReceiptRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\GoodsReceipts\Domain;

use App\Core\Contracts\RepositoryInterface;

interface GoodsReceiptRepositoryInterface extends RepositoryInterface
{
    public function generateReceiptNumber(int $companyId): string;
    public function bulkInsertItems(int $receiptId, array $items): void;
}