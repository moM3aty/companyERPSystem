<?php
// Path: app/Core/Security/SecretManager.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Exceptions\ConfigurationException;

/**
 * Enterprise Secret Manager
 * مسؤول عن إدارة المفاتيح الحساسة (API Keys, Webhook Secrets) بأمان.
 * يضمن عدم تسريبها في السجلات (Logs) أو طباعتها بالخطأ في الـ Exceptions.
 */
class SecretManager
{
    protected EncryptionManager $encryption;

    public function __construct(EncryptionManager $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * جلب سر من بيئة التشغيل أو فك تشفيره إذا كان مخزناً كنص مشفر.
     *
     * @param string $key
     * @param bool $isEncrypted
     * @return string
     * @throws ConfigurationException
     */
    public function getSecret(string $key, bool $isEncrypted = false): string
    {
        $value = getenv($key);

        if ($value === false || $value === '') {
            throw new ConfigurationException("Critical Secret [{$key}] is missing from the environment.");
        }

        if ($isEncrypted) {
            try {
                return $this->encryption->decrypt($value);
            } catch (\Throwable $e) {
                throw new ConfigurationException("Failed to decrypt secret [{$key}]. It may be corrupted.", 500, $e);
            }
        }

        return $value;
    }

    /**
     * إخفاء البيانات الحساسة (Masking) لعرض آخر 4 أرقام فقط (مفيد لواجهات المستخدم).
     *
     * @param string $secret
     * @param string $maskChar
     * @return string
     */
    public function mask(string $secret, string $maskChar = '*'): string
    {
        $length = strlen($secret);
        
        if ($length <= 4) {
            return str_repeat($maskChar, $length);
        }

        return str_repeat($maskChar, $length - 4) . substr($secret, -4);
    }
}