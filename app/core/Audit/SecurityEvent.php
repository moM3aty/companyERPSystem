<?php
// Path: app/Core/Audit/SecurityEvent.php

declare(strict_types=1);

namespace App\Core\Audit;

use App\Core\Events\Event;

/**
 * Enterprise Security Event
 * حدث مخصص للعمليات الأمنية الحساسة (محاولات اختراق، تغيير كلمات المرور، ترقية صلاحيات).
 */
class SecurityEvent extends Event
{
    public readonly string $securityAction;
    public readonly string $description;
    public readonly array $metadata;
    public readonly ?int $userId;

    /**
     * SecurityEvent constructor.
     *
     * @param string $securityAction (e.g., 'brute_force_detected', 'password_changed')
     * @param string $description
     * @param array $metadata
     * @param int|null $userId
     */
    public function __construct(string $securityAction, string $description, array $metadata = [], ?int $userId = null)
    {
        parent::__construct();
        $this->securityAction = $securityAction;
        $this->description = $description;
        $this->metadata = $metadata;
        $this->userId = $userId;
    }

    /**
     * @inheritDoc
     */
    public function toPayload(): array
    {
        return [
            'event_id'        => $this->eventId,
            'occurred_on'     => $this->occurredOn,
            'security_action' => $this->securityAction,
            'description'     => $this->description,
            'metadata'        => $this->metadata,
            'user_id'         => $this->userId,
        ];
    }
}