<?php
// Path: app/Modules/Purchasing/Invoices/Domain/PurchaseInvoiceRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Domain;

use App\Core\Contracts\RepositoryInterface;

interface PurchaseInvoiceRepositoryInterface extends RepositoryInterface
{
    public function generateInvoiceNumber(int $companyId): string;
    public function bulkInsertItems(int $invoiceId, array $items): void;
}