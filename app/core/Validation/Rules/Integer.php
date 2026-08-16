<?php
// Path: app/Core/Validation/Rules/Integer.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Integer
 * يضمن أن القيمة المدخلة هي عدد صحيح فقط (بدون كسور).
 */
class Integer implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} must be an integer.";
    }
}