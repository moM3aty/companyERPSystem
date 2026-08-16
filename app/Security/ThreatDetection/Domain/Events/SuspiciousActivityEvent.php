<?php
// Path: app/Security/ThreatDetection/Domain/Events/SuspiciousActivityEvent.php

declare(strict_types=1);

namespace App\Security\ThreatDetection\Domain\Events;

use App\Core\Events\Event;

/**
 * يطلق عند اكتشاف نشاط مريب (مثل تسجيل دخول من دولة مختلفة فجأة، أو محاولات تخمين كثيرة).
 */
class SuspiciousActivityEvent extends Event
{
    public readonly int $userId;
    public readonly string $threatType; // e.g., 'impossible_travel', 'ip_mismatch'
    public readonly string $description;
    public readonly array $metadata;

    public function __construct(int $userId, string $threatType, string $description, array $metadata = [])
    {
        parent::__construct();
        $this->userId = $userId;
        $this->threatType = $threatType;
        $this->description = $description;
        $this->metadata = $metadata;
    }

    public function toPayload(): array
    {
        return [
            'event_id'    => $this->eventId,
            'occurred_on' => $this->occurredOn,
            'user_id'     => $this->userId,
            'threat_type' => $this->threatType,
            'description' => $this->description,
            'metadata'    => $this->metadata,
        ];
    }
}