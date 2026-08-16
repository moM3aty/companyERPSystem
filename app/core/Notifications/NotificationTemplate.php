<?php
// Path: app/Core/Notifications/NotificationTemplate.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Models\Entity;

/**
 * Enterprise Notification Template
 * يمثل قالب الإشعار في قاعدة البيانات ليتمكن مدير النظام من تعديل النصوص ديناميكياً.
 */
class NotificationTemplate extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'notification_type' => 'string', // e.g., 'invoice_created'
        'channel' => 'string', // 'email', 'sms', 'in_app'
        'subject' => 'string',
        'body' => 'string', // يدعم المتغيرات مثل {user_name}
        'is_active' => 'boolean',
    ];

    /**
     * استبدال المتغيرات في القالب بالبيانات الفعلية.
     *
     * @param string $text
     * @param array $data
     * @return string
     */
    public function compile(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $text = str_replace('{' . $key . '}', (string) $value, $text);
            }
        }
        return $text;
    }
}