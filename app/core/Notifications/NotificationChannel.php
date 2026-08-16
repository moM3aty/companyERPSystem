<?php
// Path: app/Core/Notifications/NotificationChannel.php

declare(strict_types=1);

namespace App\Core\Notifications;

/**
 * Enterprise Notification Channel Interface
 * العقد الذي يلزم أي قناة إشعارات (إيميل، SMS، FCM) بتنفيذه لضمان توحيد آلية الإرسال.
 */
interface NotificationChannel
{
    /**
     * إرسال الإشعار للمستخدم المستهدف.
     *
     * @param Notification $notification كائن الإشعار المحتوي على البيانات
     * @param array $user البيانات الأساسية للمستخدم (ID, Email, Phone, FCM Token)
     * @return bool حالة نجاح الإرسال
     */
    public function send(Notification $notification, array $user): bool;
}