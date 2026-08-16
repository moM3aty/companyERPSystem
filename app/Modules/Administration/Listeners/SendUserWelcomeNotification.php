<?php
// Path: app/Modules/Administration/Listeners/SendUserWelcomeNotification.php

declare(strict_types=1);

namespace App\Modules\Administration\Listeners;

use App\Core\Events\EventListener;
use App\Core\Events\Event;
use App\Core\Notifications\NotificationManager;
use App\Modules\Administration\Events\UserCreated;

/**
 * Enterprise Event Listener: Send User Welcome Notification
 * يستمع لحدث (UserCreated) ويقوم بإرسال إيميل الترحيب عبر הـ NotificationManager.
 */
class SendUserWelcomeNotification implements EventListener
{
    protected NotificationManager $notifier;

    public function __construct(NotificationManager $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * @inheritDoc
     */
    public function handle(Event $event): void
    {
        // حماية النوع (Type Safety)
        if (!$event instanceof UserCreated) {
            return;
        }

        // إرسال الإشعار للمستخدم الجديد (الـ Manager سيتكفل بالبحث عن الإيميل وتوليد الـ Template)
        $this->notifier->send(
            (int) $event->entityId, // userId
            'welcome_notification',
            ['username' => $event->username, 'company_id' => $event->companyId],
            ['email', 'in_app']
        );
    }
}