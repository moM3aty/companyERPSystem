<?php
// Path: app/Core/Validation/Rules/Email.php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rule;

/**
 * Enterprise Validation Rule: Email
 * يضمن صحة صياغة البريد الإلكتروني.
 */
class Email implements Rule
{
    /**
     * @inheritDoc
     */
    public function passes(string $field, mixed $value, array $data): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @inheritDoc
     */
    public function message(string $field): string
    {
        return "The {$field} must be a valid email address.";
    }
}