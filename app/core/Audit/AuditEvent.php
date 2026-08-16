<?php
// Path: app/Core/Audit/AuditEvent.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Events\Event;

/**
 * Enterprise Audit Event
 * حدث (Event) يتم إطلاقه فور حدوث أي تغيير على كيان (Entity) لدفع البيانات لمحرك التدقيق.
 */
class AuditEvent extends Event
{
    public readonly string $action;
    public readonly string $entityType;
    public readonly int|string $entityId;
    public readonly array $oldValues;
    public readonly array $newValues;
    public readonly ?int $userId;
    public readonly ?int $companyId;

    /**
     * AuditEvent constructor.
     *
     * @param string $action ('created', 'updated', 'deleted')
     * @param string $entityType
     * @param int|string $entityId
     * @param array $oldValues
     * @param array $newValues
     * @param int|null $userId
     * @param int|null $companyId
     */
    public function __construct(
        string $action,
        string $entityType,
        int|string $entityId,
        array $oldValues = [],
        array $newValues = [],
        ?int $userId = null,
        ?int $companyId = null
    ) {
        parent::__construct();
        $this->action = $action;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->oldValues = $oldValues;
        $this->newValues = $newValues;
        $this->userId = $userId;
        $this->companyId = $companyId;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return [
            'event_id'    => $this->eventId,
            'occurred_on' => $this->occurredOn,
            'action'      => $this->action,
            'entity_type' => $this->entityType,
            'entity_id'   => $this->entityId,
            'old_values'  => $this->oldValues,
            'new_values'  => $this->newValues,
            'user_id'     => $this->userId,
            'company_id'  => $this->companyId,
        ];
    }
}