<?php
// Path: app/Core/Auth/AccountLockService.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;

/**
 * Enterprise Account Lock Service
 * يدير عملية حظر الحسابات بعد فشل تسجيل الدخول لعدد معين من المرات لمنع هجمات (Brute Force).
 */
class AccountLockService
{
    protected DatabaseManager $db;
    
    /**
     * الحد الأقصى للمحاولات قبل الحظر.
     */
    protected int $maxFailedAttempts = 5;
    
    /**
     * مدة الحظر التلقائي بالدقائق (0 = حظر دائم يحتاج لتدخل الإدارة).
     */
    protected int $lockoutDurationMinutes = 30;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * تسجيل محاولة فاشلة.
     *
     * @param int $userId
     * @return bool يُرجع True إذا تم قفل الحساب في هذه المحاولة.
     */
    public function recordFailedAttempt(int $userId): bool
    {
        $user = $this->db->connection()->selectOne("SELECT failed_login_attempts FROM users WHERE id = ?", [$userId]);
        
        if (!$user) {
            return false;
        }

        $attempts = (int) $user['failed_login_attempts'] + 1;

        if ($attempts >= $this->maxFailedAttempts) {
            $lockoutTime = $this->lockoutDurationMinutes > 0 
                ? date('Y-m-d H:i:s', strtotime("+{$this->lockoutDurationMinutes} minutes")) 
                : '2099-12-31 23:59:59'; // شبه دائم

            $this->db->connection()->update(
                "UPDATE users SET failed_login_attempts = ?, locked_until = ? WHERE id = ?",
                [$attempts, $lockoutTime, $userId]
            );
            return true; // تم القفل
        }

        $this->db->connection()->update(
            "UPDATE users SET failed_login_attempts = ? WHERE id = ?",
            [$attempts, $userId]
        );

        return false;
    }

    /**
     * تصفير عداد المحاولات الفاشلة عند تسجيل الدخول بنجاح.
     *
     * @param int $userId
     * @return void
     */
    public function clearFailedAttempts(int $userId): void
    {
        $this->db->connection()->update(
            "UPDATE users SET failed_login_attempts = 0, locked_until = NULL WHERE id = ?",
            [$userId]
        );
    }

    /**
     * التحقق مما إذا كان الحساب مقفولاً حالياً.
     *
     * @param int $userId
     * @return bool
     */
    public function isAccountLocked(int $userId): bool
    {
        $user = $this->db->connection()->selectOne("SELECT locked_until FROM users WHERE id = ?", [$userId]);
        
        if (!$user || !$user['locked_until']) {
            return false;
        }

        $lockedUntil = strtotime($user['locked_until']);
        
        if (time() < $lockedUntil) {
            return true;
        }

        // انتهت فترة الحظر، نقوم بفك الحظر تلقائياً
        $this->clearFailedAttempts($userId);
        
        return false;
    }
}