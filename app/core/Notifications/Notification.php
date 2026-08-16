<?php
// Path: app/Core/Notifications/Notification.php

declare(strict_types=1);

namespace App\Core\Notifications;

/**
 * Enterprise Notification DTO
 * يحمل محتوى الإشعار ليتم تمريره عبر قنوات الإرسال المختلفة.
 */
class Notification
{
    public readonly string $type;
    public readonly string $title;
    public readonly string $body;
    public readonly array $data;
    public readonly array $channels;

    /**
     * Notification constructor.
     *
     * @param string $type نوع الإشعار (مثال: 'invoice_created')
     * @param string $title عنوان الإشعار
     * @param string $body محتوى الإشعار
     * @param array $data بيانات إضافية (مثل: ['invoice_id' => 50])
     * @param array $channels القنوات المطلوبة للإرسال ['in_app', 'email', 'sms', 'push']
     */
    public function __construct(string $type, string $title, string $body, array $data = [], array $channels = ['in_app'])
    {
        $this->type = $type;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->channels = $channels;
    }
}