<?php
// Path: app/Core/Validation/Rules/Required.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Required
 * يضمن أن الحقل موجود ويحتوي على قيمة فعلية غير فارغة.
 */
class Required implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && trim($value) === '') {
            return false;
        }

        if (is_countable($value) && count($value) === 0) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} field is required.";
    }
}