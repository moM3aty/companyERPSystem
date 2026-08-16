<?php
// Path: app/Modules/Sales/CustomerPayments/Domain/InvoiceAllocationRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Domain;

use App\Core\Contracts\RepositoryInterface;

interface InvoiceAllocationRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب المبالغ غير المخصصة (المتبقية) من سند قبض معين.
     *
     * @param int $receiptId
     * @param int $companyId
     * @return float
     */
    public function getUnallocatedReceiptBalance(int $receiptId, int $companyId): float;
}