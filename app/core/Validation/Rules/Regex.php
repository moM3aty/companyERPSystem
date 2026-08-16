<?php
// Path: app/Core/Validation/Rules/Regex.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Regex
 * يتحقق من أن القيمة المدخلة تتطابق مع تعبير نمطي (Regular Expression) محدد.
 * ممتاز للتحقق من صياغات معقدة مثل (أرقام الهواتف، التواريخ المخصصة، أو الرموز البريدية).
 */
class Regex implements Rule
{
    protected string $pattern;

    /**
     * Regex constructor.
     *
     * @param string $pattern التعبير النمطي المراد المطابقة معه (مثال: '/^20\d{2}-(0[1-9]|1[0-2])$/')
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        // نتجاوز الفحص إذا كانت القيمة فارغة (مهمة الـ Required rule)
        if ($value === null || $value === '') {
            return true;
        }

        if (!is_scalar($value)) {
            return false;
        }

        // استخدام preg_match للتحقق من التطابق التام
        return preg_match($this->pattern, (string) $value) > 0;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} format is invalid and does not match the required pattern.";
    }
}