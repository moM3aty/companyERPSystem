<?php
// Path: app/Core/Files/FileSecurity.php

declare(strict_types=1);

namespace App\Core\Files;

/**
 * Enterprise File Security Service
 * يقوم بفحص الملفات ضد الثغرات الخبيثة (مثل ملفات PHP المخبأة داخل صور أو PDF).
 */
class FileSecurity
{
    /**
     * فحص الملف ضد التوقيعات الخبيثة.
     *
     * @param string $filePath مسار الملف المؤقت
     * @return bool True if safe, False if malicious
     */
    public function scan(string $filePath): bool
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        // قائمة بالأنماط التي لا يجب أن تتواجد داخل ملفات الصور والمستندات المرفوعة
        $maliciousPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script\b[^>]*>/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/base64_decode\s*\(/i'
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false; // تم اكتشاف نمط خبيث
            }
        }

        return true; // الملف آمن
    }
}