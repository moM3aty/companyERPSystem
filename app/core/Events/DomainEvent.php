<?php
// Path: app/Core/Events/DomainEvent.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Domain Event
 * يمثل حدثاً وقع داخل النطاق الأساسي للنظام (Domain).
 * مثال: InvoicePosted, UserRegistered, StockDepleted.
 */
abstract class DomainEvent extends Event
{
    /**
     * @var int|string معرف الكيان المرتبط بالحدث (مثلاً: رقم الفاتورة)
     */
    public readonly int|string $entityId;

    /**
     * DomainEvent constructor.
     *
     * @param int|string $entityId
     */
    public function __construct(int|string $entityId)
    {
        parent::__construct();
        $this->entityId = $entityId;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return [
            'event_id'    => $this->eventId,
            'occurred_on' => $this->occurredOn,
            'event_name'  => $this->getName(),
            'entity_id'   => $this->entityId,
        ];
    }
}