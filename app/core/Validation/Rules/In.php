<?php
// Path: app/Core/Validation/Rules/In.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: In
 * يضمن أن القيمة المدخلة تقع ضمن نطاق مصفوفة محددة مسبقاً (مثال: حالات الفاتورة).
 */
class In implements Rule
{
    protected array $allowedValues;

    /**
     * In constructor.
     *
     * @param array $allowedValues
     */
    public function __construct(array $allowedValues)
    {
        $this->allowedValues = $allowedValues;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return in_array($value, $this->allowedValues, strict: false); // استخدام strict false للسماح بمطابقة 1 مع '1'
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        $allowed = implode(', ', $this->allowedValues);
        return "The selected {$field} is invalid. Allowed values: [{$allowed}].";
    }
}