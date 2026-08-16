<?php
// Path: app/Core/Validation/Rules/Decimal.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Decimal
 * يضمن أن القيمة المدخلة رقمية وتحتوي على عدد محدد من الخانات العشرية (مهم للأنظمة المالية).
 */
class Decimal implements Rule
{
    protected int $min;
    protected int $max;

    /**
     * Decimal constructor.
     *
     * @param int $min الحد الأدنى للخانات العشرية
     * @param int $max الحد الأقصى للخانات العشرية
     */
    public function __construct(int $min = 2, int $max = 2)
    {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (!is_numeric($value)) {
            return false;
        }

        // صيغة الـ Regex لضمان الدقة العشرية المطلوبة
        $pattern = sprintf('/^-?[0-9]+(\.[0-9]{%d,%d})?$/', $this->min, $this->max);
        
        return preg_match($pattern, (string) $value) > 0;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        if ($this->min === $this->max) {
            return "The {$field} must be a decimal with exactly {$this->max} decimal places.";
        }
        
        return "The {$field} must be a decimal between {$this->min} and {$this->max} decimal places.";
    }
}