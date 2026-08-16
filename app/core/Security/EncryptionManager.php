<?php
// Path: app/Core/Security/EncryptionManager.php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Config\SecurityConfig;
use RuntimeException;
use InvalidArgumentException;

/**
 * Enterprise Encryption Manager
 * تشفير وفك تشفير البيانات الحساسة (مثل مفاتيح الـ API للبوابات، أرقام الحسابات السرية)
 * باستخدام خوارزمية AES-256-CBC القوية، مع توليد IV عشوائي لكل عملية تشفير لضمان أعلى معايير الأمان.
 */
class EncryptionManager
{
    protected string $key;
    protected string $cipher = 'aes-256-cbc';

    /**
     * EncryptionManager constructor.
     *
     * @param SecurityConfig $config
     */
    public function __construct(SecurityConfig $config)
    {
        // المفتاح يجب أن يكون Base64 لـ 32 بايت (256-bit)
        $key = $config->encryptionKey;

        if (empty($key)) {
            throw new RuntimeException('Encryption key is missing from configuration.');
        }

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        if (strlen($key) !== 32) {
            throw new RuntimeException('The environment encryption key must be exactly 32 bytes long for AES-256-CBC.');
        }

        $this->key = $key;
    }

    /**
     * تشفير قيمة نصية بمسار اتجاهين (Two-way encryption).
     *
     * @param string $value القيمة المراد تشفيرها
     * @return string النص المشفر بصيغة Base64
     * @throws RuntimeException
     */
    public function encrypt(string $value): string
    {
        $ivLength = openssl_cipher_iv_length($this->cipher);
        $iv = random_bytes($ivLength);

        // تشفير القيمة
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);

        if ($encrypted === false) {
            throw new RuntimeException('Could not encrypt the data.');
        }

        // توليد توقيع (MAC) لضمان عدم التلاعب بالنص المشفر (Tamper-proofing)
        $mac = hash_hmac('sha256', $iv . $encrypted, $this->key);

        // دمج المتجه (IV) مع النص المشفر مع التوقيع وتحويلهم لـ JSON ثم Base64
        $payload = json_encode([
            'iv' => base64_encode($iv),
            'value' => $encrypted,
            'mac' => $mac,
        ]);

        return base64_encode($payload);
    }

    /**
     * فك تشفير نص تم تشفيره بواسطة الدالة encrypt.
     *
     * @param string $payload النص المشفر
     * @return string القيمة الأصلية
     * @throws RuntimeException|InvalidArgumentException
     */
    public function decrypt(string $payload): string
    {
        $payload = json_decode(base64_decode($payload), true);

        if (!$this->validPayload($payload)) {
            throw new InvalidArgumentException('The payload is invalid or tampered with.');
        }

        $iv = base64_decode($payload['iv']);
        $encrypted = $payload['value'];

        // التحقق من التوقيع لمنع هجمات (Padding Oracle Attacks)
        $calculatedMac = hash_hmac('sha256', $iv . $encrypted, $this->key);
        if (!hash_equals($payload['mac'], $calculatedMac)) {
            throw new InvalidArgumentException('MAC validation failed. The data has been tampered with.');
        }

        $decrypted = openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);

        if ($decrypted === false) {
            throw new RuntimeException('Could not decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * التحقق من هيكلية المصفوفة المشفرة.
     *
     * @param mixed $payload
     * @return bool
     */
    protected function validPayload(mixed $payload): bool
    {
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']) &&
               strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length($this->cipher);
    }
}