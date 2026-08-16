<?php
// Path: app/Security/DeviceManagement/Domain/Events/NewDeviceRegisteredEvent.php

declare(strict_types=1);

namespace App\Security\DeviceManagement\Domain\Events;

use App\Core\Events\Event;

/**
 * يطلق عند تسجيل دخول المستخدم من جهاز/متصفح جديد تماماً.
 * يرسل إيميل تحذيري (Security Alert) للمستخدم ليؤكد ما إذا كان هو.
 */
class NewDeviceRegisteredEvent extends Event
{
    public readonly int $userId;
    public readonly string $deviceName;
    public readonly string $ipAddress;
    public readonly string $location;

    public function __construct(int $userId, string $deviceName, string $ipAddress, string $location = 'Unknown')
    {
        parent::__construct();
        $this->userId = $userId;
        $this->deviceName = $deviceName;
        $this->ipAddress = $ipAddress;
        $this->location = $location;
    }

    public function toPayload(): array
    {
        return [
            'event_id'    => $this->eventId,
            'occurred_on' => $this->occurredOn,
            'user_id'     => $this->userId,
            'device_name' => $this->deviceName,
            'ip_address'  => $this->ipAddress,
            'location'    => $this->location,
        ];
    }
}