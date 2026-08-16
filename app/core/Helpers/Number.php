<?php
// Path: app/Core/Helpers/Number.php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Enterprise Number Helper
 * Ensures strict financial rounding and formatting for ERP systems.
 */
class Number
{
    /**
     * Format a number as a currency string.
     *
     * @param float $amount
     * @param string $currencySymbol
     * @param int $decimals
     * @return string
     */
    public static function currency(float $amount, string $currencySymbol = '', int $decimals = 2): string
    {
        $formatted = number_format($amount, $decimals, '.', ',');
        
        return $currencySymbol ? "{$currencySymbol} {$formatted}" : $formatted;
    }

    /**
     * Format a number as a percentage.
     *
     * @param float $value
     * @param int $decimals
     * @return string
     */
    public static function percentage(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals, '.', '') . '%';
    }

    /**
     * Round a float strictly for financial calculations.
     * Uses PHP's PHP_ROUND_HALF_UP which is standard for accounting.
     *
     * @param float $value
     * @param int $precision
     * @return float
     */
    public static function roundFinancial(float $value, int $precision = 2): float
    {
        return round($value, $precision, PHP_ROUND_HALF_UP);
    }

    /**
     * Parse a formatted numeric string back to a float.
     * (e.g., "1,234,567.89" -> 1234567.89)
     *
     * @param string $value
     * @return float
     */
    public static function parse(string $value): float
    {
        // Remove spaces and commas
        $cleanString = str_replace([' ', ','], '', $value);
        
        return (float) $cleanString;
    }
}