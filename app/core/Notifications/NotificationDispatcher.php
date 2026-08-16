<?php
// Path: app/Core/Notifications/NotificationDispatcher.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Bootstrap\Container;

/**
 * Enterprise Notification Dispatcher
 * يقوم بتوجيه الإشعار إلى القنوات الصحيحة بناءً على طلب النظام وتفضيلات المستخدم.
 */
class NotificationDispatcher
{
    protected NotificationPreference $preference;
    protected Container $container;

    /**
     * خريطة ربط أسماء القنوات بكلاسات التنفيذ.
     */
    protected array $channelMap = [
        'in_app' => InAppChannel::class,
        'email'  => EmailChannel::class,
        'sms'    => SmsChannel::class,
        'push'   => PushChannel::class,
    ];

    public function __construct(NotificationPreference $preference, Container $container)
    {
        $this->preference = $preference;
        $this->container = $container;
    }

    /**
     * توزيع الإشعار على القنوات المطلوبة.
     *
     * @param Notification $notification
     * @param array $user
     * @return void
     */
    public function dispatch(Notification $notification, array $user): void
    {
        foreach ($notification->channels as $channelName) {
            
            // 1. التحقق من القناة
            if (!isset($this->channelMap[$channelName])) {
                continue;
            }

            // 2. التحقق من تفضيلات المستخدم (هل عطل هذه القناة؟)
            if (!$this->preference->isChannelEnabledForUser((int)$user['id'], $notification->type, $channelName)) {
                continue;
            }

            // 3. جلب الـ Channel Instance من الـ Container وحقن اعتماداته تلقائياً
            /** @var NotificationChannel $channelInstance */
            $channelInstance = $this->container->make($this->channelMap[$channelName]);

            // 4. الإرسال (يُفضل في الأنظمة الكبيرة أن يتم عمل Queue لهذه الخطوة)
            $channelInstance->send($notification, $user);
        }
    }
}