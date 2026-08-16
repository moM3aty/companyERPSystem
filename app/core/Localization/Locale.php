<?php
// Path: app/Core/Localization/Locale.php

declare(strict_types=1);

namespace App\Core\Localization;

use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Locale Value Object
 * يمثل المعيار الثقافي واللغوي (مثال: ar-SA, en-US).
 * يضمن أن أي كود لغوي يمر في النظام هو كود صحيح ومقبول دولياً.
 */
class Locale
{
    public readonly string $code;
    public readonly string $language;
    public readonly ?string $region;

    /**
     * Locale constructor.
     *
     * @param string $code (e.g., 'ar', 'en-US', 'fr-FR')
     * @throws BusinessException
     */
    public function __construct(string $code)
    {
        $code = trim($code);
        
        // التحقق من صيغة الكود باستخدام Regular Expression
        if (!preg_match('/^[a-z]{2}(-[A-Z]{2})?$/', $code)) {
            throw new BusinessException("Invalid locale format: [{$code}]. Expected formats: 'en' or 'en-US'.", 422);
        }

        $this->code = $code;
        
        $parts = explode('-', $code);
        $this->language = $parts[0];
        $this->region = $parts[1] ?? null;
    }

    /**
     * جلب رمز اللغة فقط (بدون المنطقة).
     *
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * تحويل الكائن إلى نص (Magic Method).
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->code;
    }
}