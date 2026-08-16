<?php
// Path: app/Modules/Treasury/Receipts/Domain/Events/ReceiptPostedEvent.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Receipt Posted
 * يُطلق بعد حفظ سند القبض وتوليد القيد المحاسبي له لتبليغ باقي النظام (مثل الـ Notifications).
 */
class ReceiptPostedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly float $amount;

    /**
     * ReceiptPostedEvent constructor.
     *
     * @param int $receiptId
     * @param int $companyId
     * @param float $amount
     */
    public function __construct(int $receiptId, int $companyId, float $amount)
    {
        parent::__construct($receiptId);
        $this->companyId = $companyId;
        $this->amount = $amount;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id' => $this->companyId,
            'amount'     => $this->amount,
            'status'     => 'posted',
        ]);
    }
}