<?php
// Path: app/Core/Notifications/NotificationPreference.phpNotificationPreference.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Notification Preference Manager
 * يفحص ما إذا كان المستخدم قد قام بإلغاء تفعيل نوع معين من الإشعارات أو قناة معينة (Opt-out).
 */
class NotificationPreference
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * التحقق مما إذا كانت القناة مفعلة لهذا المستخدم لهذا النوع من الإشعارات.
     *
     * @param int $userId
     * @param string $notificationType
     * @param string $channel
     * @return bool
     */
    public function isChannelEnabledForUser(int $userId, string $notificationType, string $channel): bool
    {
        $sql = "SELECT is_enabled FROM user_notification_preferences 
                WHERE user_id = ? AND notification_type = ? AND channel = ? LIMIT 1";

        $preference = $this->db->connection()->selectOne($sql, [$userId, $notificationType, $channel]);

        // الافتراضي (إذا لم يقم المستخدم بتغيير الإعدادات) هو أن الإشعارات مفعلة
        if (!$preference) {
            return true;
        }

        return (int) $preference['is_enabled'] === 1;
    }
}