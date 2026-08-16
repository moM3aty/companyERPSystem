<?php
// Path: app/Core/Notifications/SmsChannel.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Config\Config;
use App\Core\Contracts\LoggerInterface;

/**
 * Enterprise SMS Channel
 * يقوم بإرسال رسالة نصية قصيرة عبر مزود خارجي (مثل Twilio أو Unifonic).
 */
class SmsChannel implements NotificationChannel
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
        $phone = $user['phone'] ?? null;

        if (!$phone) {
            $this->logger->warning("SmsChannel: User ID {$user['id']} does not have a valid phone number.");
            return false;
        }

        $apiUrl = $this->config->get('sms.api_url', 'https://api.smsprovider.com/send');
        $apiKey = $this->config->get('sms.api_key', '');

        if (empty($apiKey)) {
            $this->logger->error("SmsChannel: SMS API Key is not configured.");
            return false;
        }

        $payload = [
            'recipient' => $phone,
            'message'   => strip_tags($notification->body), // الـ SMS لا تقبل HTML
            'sender_id' => $this->config->get('sms.sender_id', 'ERP_SYS')
        ];

        // تنفيذ طلب cURL لإرسال الـ SMS
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        $this->logger->error("SmsChannel: Failed to send SMS to {$phone}. API responded with code: {$httpCode}. Response: {$response}");
        return false;
    }
}