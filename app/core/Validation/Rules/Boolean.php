<?php
// Path: app/Core/Validation/Rules/Boolean.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Boolean
 * يضمن أن القيمة تعبر عن حالة منطقية (صح/خطأ).
 * يقبل القيم (true, false, 1, 0, "1", "0", "true", "false").
 */
class Boolean implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $acceptable = [true, false, 1, 0, '1', '0', 'true', 'false'];

        return in_array($value, $acceptable, true);
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} field must be true or false.";
    }
}