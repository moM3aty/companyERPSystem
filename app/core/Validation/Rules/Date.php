<?php
// Path: app/Core/Validation/Rules/Date.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;
use DateTime;

/**
 * Enterprise Validation Rule: Date
 * يضمن أن القيمة المدخلة عبارة عن تاريخ صحيح بناءً على صيغة محددة.
 */
class Date implements Rule
{
    protected string $format;

    /**
     * Date constructor.
     *
     * @param string $format (افتراضياً Y-m-d)
     */
    public function __construct(string $format = 'Y-m-d')
    {
        $this->format = $format;
    }

    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        $date = DateTime::createFromFormat($this->format, $value);

        return $date !== false && $date->format($this->format) === $value;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} does not match the format {$this->format}.";
    }
}