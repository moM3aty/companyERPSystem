<?php
// Path: app/Core/Helpers/Regex.php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Enterprise Regex Helper
 * Contains pre-defined secure regular expressions for common ERP validation tasks.
 */
class Regex
{
    /**
     * Check if a string contains only letters and numbers.
     *
     * @param string $value
     * @return bool
     */
    public static function isAlphaNumeric(string $value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

    /**
     * Check if a string is a valid strong password.
     * Requires at least 8 characters, 1 uppercase, 1 lowercase, 1 number, and 1 special character.
     *
     * @param string $password
     * @return bool
     */
    public static function isStrongPassword(string $password): bool
    {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        return (bool) preg_match($pattern, $password);
    }

    /**
     * Extract all email addresses from a given text.
     *
     * @param string $text
     * @return array
     */
    public static function extractEmails(string $text): array
    {
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        preg_match_all($pattern, $text, $matches);
        
        return $matches[0] ?? [];
    }
}