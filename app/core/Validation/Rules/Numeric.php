<?php
// Path: app/Core/Validation/Rules/Numeric.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Numeric
 * يضمن أن القيمة المدخلة عبارة عن رقم (صحيح أو عشري).
 */
class Numeric implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return is_numeric($value);
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} must be a number.";
    }
}