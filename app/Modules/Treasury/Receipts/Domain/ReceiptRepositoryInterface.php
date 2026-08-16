<?php
// Path: app/Modules/Treasury/Receipts/Domain/ReceiptRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Receipt
 */
interface ReceiptRepositoryInterface extends RepositoryInterface
{
    /**
     * توليد رقم تسلسلي آمن لسند القبض.
     *
     * @param int $companyId
     * @return string
     */
    public function generateReceiptNumber(int $companyId): string;
}