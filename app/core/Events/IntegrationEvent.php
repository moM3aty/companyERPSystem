<?php
// Path: app/Core/Events/IntegrationEvent.php

declare(strict_types=1);

namespace App\Core\Events;

/**
 * Enterprise Integration Event
 * يمثل حدثاً مخصصاً ليتم نشره خارج حدود النظام (إلى خدمات أخرى عبر Webhooks أو Message Brokers).
 */
abstract class IntegrationEvent extends Event
{
    public readonly string $sourceSystem;

    /**
     * IntegrationEvent constructor.
     *
     * @param string $sourceSystem اسم النظام المُصدر للحدث
     */
    public function __construct(string $sourceSystem = 'core_erp')
    {
        parent::__construct();
        $this->sourceSystem = $sourceSystem;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return [
            'event_id'      => $this->eventId,
            'occurred_on'   => $this->occurredOn,
            'event_name'    => $this->getName(),
            'source_system' => $this->sourceSystem,
        ];
    }
}