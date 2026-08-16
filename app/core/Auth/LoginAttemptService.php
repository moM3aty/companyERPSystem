<?php
// Path: app/Core/Auth/LoginAttemptService.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Login Attempt Service
 * يسجل عمليات تسجيل الدخول الناجحة والفاشلة لغرض المراقبة الأمنية (Audit & SIEM).
 */
class LoginAttemptService
{
    protected DatabaseManager $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل نتيجة محاولة تسجيل دخول.
     *
     * @param string $email
     * @param string $ipAddress
     * @param string $userAgent
     * @param bool $isSuccess
     * @param int|null $userId
     * @return void
     */
    public function logAttempt(string $email, string $ipAddress, string $userAgent, bool $isSuccess, ?int $userId = null): void
    {
        $this->db->connection()->insert(
            "INSERT INTO login_history (user_id, email, ip_address, user_agent, is_success, attempted_at) 
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $userId,
                $email,
                $ipAddress,
                $userAgent,
                $isSuccess ? 1 : 0,
                date('Y-m-d H:i:s')
            ]
        );
    }
}