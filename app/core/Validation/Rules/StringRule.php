<?php
// Path: app/Core/Validation/Rules/StringRule.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: String
 * يضمن أن القيمة المدخلة هي نص.
 */
class StringRule implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        // نتجاوز الفحص إذا كان الحقل فارغاً (نترك مهمة الفحص لـ Required)
        if ($value === null || $value === '') {
            return true;
        }

        return is_string($value);
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} must be a valid string.";
    }
}