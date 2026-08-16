<?php
// Path: app/Core/Notifications/Http/Controllers/NotificationController.php

declare(strict_types=1);

namespace App\Core\Notifications\Http\Controllers;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\JsonResponse;
use App\Core\Api\ApiResponse;
use App\Core\Auth\AuthManager;
use App\Core\Database\DatabaseManager;

/**
 * Enterprise API Controller: User Notifications
 * تزويد الـ Frontend بمركز الإشعارات (الجرس) وقراءة الرسائل الداخلية.
 */
class NotificationController extends Controller
{
    protected DatabaseManager $db;
    protected AuthManager $auth;

    public function __construct(DatabaseManager $db, AuthManager $auth)
    {
        $this->db = $db;
        $this->auth = $auth;

        $this->middleware(['api', 'auth']); // لا يحتاج Tenant Scope لأن الإشعارات مرتبطة بالمستخدم
    }

    /**
     * جلب أحدث الإشعارات للمستخدم.
     */
    public function index(): JsonResponse
    {
        $userId = $this->auth->user()->id;

        $sql = "SELECT * FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC LIMIT 50";

        $notifications = $this->db->connection()->select($sql, [$userId]);

        // تحويل الداتا المحفوظة كـ JSON إلى مصفوفة لتعرض بشكل صحيح في الـ API
        $formatted = array_map(function ($notif) {
            $notif['data'] = $notif['data'] ? json_decode($notif['data'], true) : [];
            return $notif;
        }, $notifications);

        $unreadCount = $this->db->connection()->selectOne(
            "SELECT COUNT(id) as cnt FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        return ApiResponse::success($formatted, 'Notifications retrieved.', 200, [
            'unread_count' => (int) $unreadCount['cnt']
        ]);
    }

    /**
     * تحديد إشعار كمقروء.
     */
    public function markAsRead(int $id): JsonResponse
    {
        $userId = $this->auth->user()->id;

        $this->db->connection()->update(
            "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        return ApiResponse::success(null, 'Marked as read.');
    }

    /**
     * تحديد كافة الإشعارات كمقروءة.
     */
    public function markAllAsRead(): JsonResponse
    {
        $userId = $this->auth->user()->id;

        $this->db->connection()->update(
            "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0",
            [$userId]
        );

        return ApiResponse::success(null, 'All notifications marked as read.');
    }
}