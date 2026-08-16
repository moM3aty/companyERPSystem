<?php
// Path: app/Modules/Purchasing/RFQ/Domain/RfqRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Domain;

use App\Core\Contracts\RepositoryInterface;

interface RfqRepositoryInterface extends RepositoryInterface
{
    public function generateRfqNumber(int $companyId): string;
    public function bulkInsertItems(int $rfqId, array $items): void;
    public function bulkInsertSuppliers(int $rfqId, array $supplierIds): void;
}