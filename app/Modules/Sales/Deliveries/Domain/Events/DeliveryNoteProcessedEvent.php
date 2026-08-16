<?php
// Path: app/Modules/Sales/Deliveries/Domain/Events/DeliveryNoteProcessedEvent.php

declare(strict_types=1);

namespace App\Modules\Sales\Deliveries\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Delivery Note Processed
 * يُطلق هذا الحدث لتبليغ الـ Accounting Engine بتسجيل قيد تكلفة البضاعة المباعة (COGS).
 */
class DeliveryNoteProcessedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly int $customerId;

    public function __construct(int $deliveryId, int $companyId, int $customerId)
    {
        parent::__construct($deliveryId);
        $this->companyId = $companyId;
        $this->customerId = $customerId;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id'  => $this->companyId,
            'customer_id' => $this->customerId,
        ]);
    }
}