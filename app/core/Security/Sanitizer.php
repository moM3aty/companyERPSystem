<?php
// Path: app/Core/Security/Sanitizer.php

declare(strict_types=1);

namespace App\Core\Security;

/**
 * Enterprise Input Sanitizer
 * Cleans user input to prevent XSS (Cross-Site Scripting) and format data correctly.
 */
class Sanitizer
{
    /**
     * Clean a string by stripping HTML tags and trimming whitespace.
     * Basic XSS protection for text inputs.
     *
     * @param string $value
     * @return string
     */
    public function cleanString(string $value): string
    {
        $value = trim($value);
        return strip_tags($value);
    }

    /**
     * Encode a string for safe output within HTML context.
     * Converts special characters to HTML entities.
     *
     * @param string $value
     * @return string
     */
    public function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Sanitize an email address.
     * Removes all illegal characters from an email.
     *
     * @param string $value
     * @return string
     */
    public function sanitizeEmail(string $value): string
    {
        return (string) filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize a URL.
     * Removes all illegal characters from a URL.
     *
     * @param string $value
     * @return string
     */
    public function sanitizeUrl(string $value): string
    {
        return (string) filter_var(trim($value), FILTER_SANITIZE_URL);
    }

    /**
     * Force a string to be purely alphabetic (A-Z, a-z).
     *
     * @param string $value
     * @return string
     */
    public function alphaOnly(string $value): string
    {
        return preg_replace('/[^a-zA-Z]/', '', $value) ?? '';
    }

    /**
     * Force a string to be purely alphanumeric (A-Z, a-z, 0-9).
     *
     * @param string $value
     * @return string
     */
    public function alphaNumericOnly(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', $value) ?? '';
    }

    /**
     * Sanitize a numeric value (removes anything that isn't a digit, plus, or minus).
     *
     * @param string|int|float $value
     * @return string
     */
    public function numericOnly(string|int|float $value): string
    {
        return preg_replace('/[^0-9+\-.]/', '', (string) $value) ?? '';
    }
}