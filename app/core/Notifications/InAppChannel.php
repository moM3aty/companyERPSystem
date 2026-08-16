<?php
// Path: app/Core/Notifications/InAppChannel.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise In-App Notification Channel
 * يقوم بتسجيل الإشعار في قاعدة البيانات ليظهر في "أيقونة الجرس" داخل واجهة النظام للعميل.
 */
class InAppChannel implements NotificationChannel
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritDoc
     */
    public function send(Notification $notification, array $user): bool
    {
        $sql = "INSERT INTO notifications (user_id, type, title, body, data, is_read, created_at) 
                VALUES (?, ?, ?, ?, ?, 0, ?)";

        $payloadJson = empty($notification->data) ? null : json_encode($notification->data, JSON_UNESCAPED_UNICODE);

        try {
            $this->db->connection()->insert($sql, [
                $user['id'],
                $notification->type,
                $notification->title,
                $notification->body,
                $payloadJson,
                date('Y-m-d H:i:s')
            ]);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}