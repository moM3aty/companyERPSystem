<?php
// Path: app/Modules/Sales/Events/PaymentReceived.php

declare(strict_types=1);

namespace App\Modules\Sales\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Payment Received
 * يُطلق هذا الحدث بمجرد استلام دفعة من عميل (أو تخصيص سند قبض لفاتورة).
 * مستمعي الحدث يمكن أن يكونوا: تحديث رصيد العميل، تنبيه مدير الحسابات، أو تفعيل خدمة معلقة.
 */
class PaymentReceived extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $customerId;
    public readonly float $amount;

    public function __construct(int $receiptId, int $companyId, int $customerId, float $amount)
    {
        parent::__construct($receiptId);
        $this->companyId = $companyId;
        $this->customerId = $customerId;
        $this->amount = $amount;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'customer_id' => $this->customerId,
            'amount'      => $this->amount,
        ]);
    }
}