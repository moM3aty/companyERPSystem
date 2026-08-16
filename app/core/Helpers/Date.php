<?php
// Path: app/Core/Helpers/Date.php

declare(strict_types=1);

namespace App\Core\Helpers;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Enterprise Date Helper
 * Provides robust date and time manipulation, strictly enforcing timezone awareness
 * which is critical for Multi-Branch/Multi-National ERP operations.
 */
class Date
{
    /**
     * Get the current date and time in a specific timezone.
     *
     * @param string $timezone
     * @return DateTime
     * @throws Exception
     */
    public static function now(string $timezone = 'Asia/Riyadh'): DateTime
    {
        return new DateTime('now', new DateTimeZone($timezone));
    }

    /**
     * Convert a given date string to the standard Database format (Y-m-d H:i:s).
     *
     * @param string $dateString
     * @param string $fromTimezone
     * @param string $toTimezone Default is UTC for database storage
     * @return string
     * @throws Exception
     */
    public static function toDb(string $dateString, string $fromTimezone = 'Asia/Riyadh', string $toTimezone = 'UTC'): string
    {
        $date = new DateTime($dateString, new DateTimeZone($fromTimezone));
        $date->setTimezone(new DateTimeZone($toTimezone));
        
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Convert a database UTC date to a specific local timezone for display.
     *
     * @param string $dbDateString
     * @param string $toTimezone
     * @param string $format
     * @return string
     * @throws Exception
     */
    public static function toLocal(string $dbDateString, string $toTimezone, string $format = 'Y-m-d H:i A'): string
    {
        $date = new DateTime($dbDateString, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($toTimezone));
        
        return $date->format($format);
    }

    /**
     * Add a specific number of days to a date.
     *
     * @param string $dateString
     * @param int $days
     * @param string $format
     * @return string
     * @throws Exception
     */
    public static function addDays(string $dateString, int $days, string $format = 'Y-m-d'): string
    {
        $date = new DateTime($dateString);
        $date->modify(($days >= 0 ? '+' : '') . $days . ' days');
        
        return $date->format($format);
    }

    /**
     * Calculate the difference in days between two dates.
     *
     * @param string $startDate
     * @param string $endDate
     * @return int
     * @throws Exception
     */
    public static function diffInDays(string $startDate, string $endDate): int
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        
        return (int) $start->diff($end)->format('%r%a');
    }

    /**
     * Check if a given string is a valid date according to a format.
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function isValid(string $date, string $format = 'Y-m-d'): bool
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
}