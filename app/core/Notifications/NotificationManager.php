<?php
// Path: app/Core/Notifications/NotificationManager.php

declare(strict_types=1);

namespace App\Core\Notifications;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Notification Manager (Facade)
 * الواجهة المركزية التي تستخدمها بقية النظام (Controllers/Services) لإرسال الإشعارات بسطر كود واحد.
 */
class NotificationManager
{
    protected DatabaseManager $db;
    protected NotificationDispatcher $dispatcher;

    public function __construct(DatabaseManager $db, NotificationDispatcher $dispatcher)
    {
        $this->db = $db;
        $this->dispatcher = $dispatcher;
    }

    /**
     * إرسال إشعار لمستخدم محدد بسلاسة.
     *
     * @param int $userId معرف المستخدم المستهدف
     * @param string $type نوع الإشعار لمعرفة القالب (مثال: 'welcome_email')
     * @param array $data المتغيرات التي سيتم استبدالها في القالب (مثال: ['user_name' => 'Ahmed'])
     * @param array $channels القنوات (الافتراضي in_app فقط)
     * @return void
     */
    public function send(int $userId, string $type, array $data = [], array $channels = ['in_app']): void
    {
        // 1. جلب بيانات المستخدم اللازمة للإرسال (Email, Phone, FCM Token)
        $user = $this->db->connection()->selectOne(
            "SELECT id, username, email, phone, fcm_token FROM users WHERE id = ? AND is_active = 1",
            [$userId]
        );

        if (!$user) {
            return; // المستخدم غير موجود أو غير مفعل
        }

        // 2. محاولة جلب قالب مخصص لهذا النوع من الإشعارات من الداتابيز
        $templateData = $this->db->connection()->selectOne(
            "SELECT subject, body FROM notification_templates WHERE notification_type = ? AND is_active = 1 LIMIT 1",
            [$type]
        );

        $template = new NotificationTemplate();