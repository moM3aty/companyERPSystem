<?php
// Path: app/Core/Validation/Rules/Min.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Min
 * يضمن الحد الأدنى (للقيمة إذا كانت رقماً، أو للطول إذا كانت نصاً، أو لعدد العناصر إذا كانت مصفوفة).
 */
class Min implements Rule
{
    protected float $min;

    /**
     * Min constructor.
     *
     * @param float $min
     */
    public function __construct(float $min)
    {
        $this->min = $min;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (is_numeric($value)) {
            return (float) $value >= $this->min;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $this->min;
        }

        if (is_array($value)) {
            return count($value) >= $this->min;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} must be at least {$this->min}.";
    }
}