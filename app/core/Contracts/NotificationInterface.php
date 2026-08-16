<?php
// Path: app/Core/Contracts/NotificationInterface.php

declare(strict_types=1);

namespace App\Core\Contracts;

/**
 * Enterprise Notification Interface
 * Standardizes how notifications (Email, SMS, Push) are structured.
 */
interface NotificationInterface
{
    /**
     * Get the delivery channels the notification should use.
     * e.g., ['mail', 'database', 'sms']
     *
     * @return array
     */
    public function getChannels(): array;

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array
     */
    public function toArray(): array;
}