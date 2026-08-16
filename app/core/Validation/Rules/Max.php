<?php
// Path: app/Core/Validation/Rules/Max.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Max
 * يضمن الحد الأقصى (للقيمة إذا كانت رقماً، أو للطول إذا كانت نصاً، أو لعدد العناصر إذا كانت مصفوفة).
 */
class Max implements Rule
{
    protected float $max;

    /**
     * Max constructor.
     *
     * @param float $max
     */
    public function __construct(float $max)
    {
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

        if (is_numeric($value)) {
            return (float) $value <= $this->max;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $this->max;
        }

        if (is_array($value)) {
            return count($value) <= $this->max;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} may not be greater than {$this->max}.";
    }
}