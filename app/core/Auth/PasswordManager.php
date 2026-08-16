<?php
// Path: app/Core/Auth/PasswordManager.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;
use App\Core\Security\HashManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Helpers\Regex;

/**
 * Enterprise Password Manager
 * يطبق سياسات أمان صارمة على كلمات المرور (الطول، التعقيد، ومنع إعادة استخدام كلمات المرور القديمة).
 */
class PasswordManager
{
    protected DatabaseManager $db;
    protected HashManager $hashManager;

    /**
     * عدد كلمات المرور السابقة التي يجب حفظها لمنع التكرار.
     */
    protected int $historyLimit = 5;

    public function __construct(DatabaseManager $db, HashManager $hashManager)
    {
        $this->db = $db;
        $this->hashManager = $hashManager;
    }

    /**
     * تغيير كلمة المرور وتطبيق سياسات الأمان.
     *
     * @param int $userId
     * @param string $newPassword
     * @return void
     * @throws BusinessException
     */
    public function updatePassword(int $userId, string $newPassword): void
    {
        // 1. التحقق من قوة كلمة المرور
        if (!Regex::isStrongPassword($newPassword)) {
            throw new BusinessException(
                "Password does not meet the security policy. It must be at least 8 characters, containing uppercase, lowercase, numbers, and symbols.",
                422
            );
        }

        // 2. التحقق من عدم استخدام كلمة المرور مسبقاً (Password History Verification)
        $this->ensurePasswordNotReused($userId, $newPassword);

        // 3. تشفير الكلمة الجديدة
        $hashedPassword = $this->hashManager->make($newPassword);

        // 4. الحفظ في قاعدة البيانات
        $this->db->connection()->beginTransaction();
        try {
            // تحديث المستخدم
            $this->db->connection()->update(
                "UPDATE users SET password_hash = ?, password_changed_at = ? WHERE id = ?",
                [$hashedPassword, date('Y-m-d H:i:s'), $userId]
            );

            // إضافة الكلمة للسجل التاريخي
            $this->logPasswordHistory($userId, $hashedPassword);

            $this->db->connection()->commit();
        } catch (\Throwable $e) {
            $this->db->connection()->rollBack();
            throw new BusinessException("Failed to update password: " . $e->getMessage(), 500, $e);
        }
    }

    /**
     * التحقق من سجل كلمات المرور السابقة لمنع التكرار.
     *
     * @param int $userId
     * @param string $plainPassword
     * @throws BusinessException
     */
    protected function ensurePasswordNotReused(int $userId, string $plainPassword): void
    {
        $history = $this->db->connection()->select(
            "SELECT password_hash FROM password_histories WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $this->historyLimit]
        );

        foreach ($history as $record) {
            if ($this->hashManager->check($plainPassword, $record['password_hash'])) {
                throw new BusinessException(
                    "Security Policy Violation: You cannot reuse any of your last {$this->historyLimit} passwords.",
                    422
                );
            }
        }
    }

    /**
     * تسجيل كلمة المرور في السجل التاريخي (History).
     *
     * @param int $userId
     * @param string $hashedPassword
     * @return void
     */
    protected function logPasswordHistory(int $userId, string $hashedPassword): void
    {
        $this->db->connection()->insert(
            "INSERT INTO password_histories (user_id, password_hash, created_at) VALUES (?, ?, ?)",
            [$userId, $hashedPassword, date('Y-m-d H:i:s')]
        );

        // تنظيف السجلات القديمة التي تتجاوز الحد المسموح
        $this->db->connection()->statement(
            "DELETE FROM password_histories WHERE user_id = ? AND id NOT IN (
                SELECT id FROM (
                    SELECT id FROM password_histories WHERE user_id = ? ORDER BY created_at DESC LIMIT ?
                ) AS temp
            )",
            [$userId, $userId, $this->historyLimit]
        );
    }
}