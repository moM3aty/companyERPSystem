<?php
// Path: app/Modules/Treasury/Payments/Domain/Events/PaymentVoucherPostedEvent.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Payment Voucher Posted
 * يُطلق بعد حفظ سند الصرف وتوليد القيد المحاسبي لتبليغ الموردين أو الموظفين.
 */
class PaymentVoucherPostedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly float $amount;

    public function __construct(int $voucherId, int $companyId, float $amount)
    {
        parent::__construct($voucherId);
        $this->companyId = $companyId;
        $this->amount = $amount;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id' => $this->companyId,
            'amount'     => $this->amount,
            'status'     => 'posted',
        ]);
    }
}