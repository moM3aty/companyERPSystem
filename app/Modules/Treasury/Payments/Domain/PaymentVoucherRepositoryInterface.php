<?php
// Path: app/Modules/Treasury/Payments/Domain/PaymentVoucherRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Payment Voucher
 */
interface PaymentVoucherRepositoryInterface extends RepositoryInterface
{
    /**
     * توليد رقم تسلسلي آمن لسند الصرف.
     *
     * @param int $companyId
     * @return string
     */
    public function generateVoucherNumber(int $companyId): string;
}