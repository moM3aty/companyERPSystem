<?php
// Path: app/Core/Auth/TwoFactorManager.php

declare(strict_types=1);

namespace App\Core\Auth;

use App\Core\Database\DatabaseManager;
use App\Core\Exceptions\BusinessException;
use App\Core\Security\EncryptionManager;

/**
 * Enterprise Two-Factor Authentication Manager
 * يدير إعداد والتحقق من المصادقة الثنائية (2FA) سواء عبر تطبيقات TOTP (مثل Google Authenticator) أو الـ OTP المؤقت.
 */
class TwoFactorManager
{
    protected DatabaseManager $db;
    protected EncryptionManager $encryption;

    public function __construct(DatabaseManager $db, EncryptionManager $encryption)
    {
        $this->db = $db;
        $this->encryption = $encryption;
    }

    /**
     * التحقق من الـ OTP المدخل من المستخدم مقابل الرمز المخزن.
     *
     * @param int $userId
     * @param string $otpCode
     * @return bool
     * @throws BusinessException
     */
    public function verifyOtp(int $userId, string $otpCode): bool
    {
        $record = $this->db->connection()->selectOne(
            "SELECT id, otp_code, expires_at FROM user_otps WHERE user_id = ? AND is_used = 0 ORDER BY id DESC LIMIT 1",
            [$userId]
        );

        if (!$record) {
            return false;
        }

        if (time() > strtotime($record['expires_at'])) {
            throw new BusinessException("The OTP code has expired. Please request a new one.", 422);
        }

        if (hash_equals($record['otp_code'], $otpCode)) {
            // إبطال الكود بعد الاستخدام الناجح (منع Replay Attacks)
            $this->db->connection()->update("UPDATE user_otps SET is_used = 1 WHERE id = ?", [$record['id']]);
            return true;
        }

        return false;
    }

    /**
     * توليد كود OTP عشوائي وآمن للمستخدم.
     *
     * @param int $userId
     * @param int $lifetimeMinutes
     * @return string
     */
    public function generateOtp(int $userId, int $lifetimeMinutes = 5): string
    {
        // إبطال الأكواد السابقة للمستخدم
        $this->db->connection()->update("UPDATE user_otps SET is_used = 1 WHERE user_id = ? AND is_used = 0", [$userId]);

        $otpCode = (string) random_int(100000, 999999); // 6 digits
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$lifetimeMinutes} minutes"));

        $this->db->connection()->insert(
            "INSERT INTO user_otps (user_id, otp_code, expires_at, created_at) VALUES (?, ?, ?, ?)",
            [$userId, $otpCode, $expiresAt, date('Y-m-d H:i:s')]
        );

        return $otpCode;
    }
}