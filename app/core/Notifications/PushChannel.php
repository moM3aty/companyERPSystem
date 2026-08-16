<?php
// Path: app/Core/Notifications/PushChannel.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Config\Config;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise Push Notification Channel (FCM)
 * يرسل إشعاراً لمتصفح الويب أو تطبيقات الموبايل باستخدام Firebase Cloud Messaging.
 */
class PushChannel implements NotificationChannel
{
    protected Config $config;
    protected LoggerInterface $logger;

    public function __construct(Config $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(Notification $notification, array $user): bool
    {
        $fcmToken = $user['fcm_token'] ?? null;

        if (!$fcmToken) {
            // طبيعي أن بعض المستخدمين لم يسمحوا بالإشعارات في المتصفح، لذا نسجلها كمعلومة وليس كخطأ.
            $this->logger->info("PushChannel: User ID {$user['id']} does not have an FCM token.");
            return false;
        }

        $serverKey = $this->config->get('push.fcm_server_key', '');

        if (empty($serverKey)) {
            $this->logger->error("PushChannel: FCM Server Key is not configured.");
            return false;
        }

        $payload = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $notification->title,
                'body'  => strip_tags($notification->body),
                'sound' => 'default'
            ],
            'data' => $notification->data
        ];

        $ch = curl_init('https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: key=' . $serverKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return true;
        }

        $this->logger->error("PushChannel: FCM Send failed. HTTP Code: {$httpCode}");
        return false;
    }
}