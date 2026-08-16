<?php
// Path: app/Core/Helpers/File.php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Enterprise File Helper
 * Safe extraction of file information for the Storage/Files modules.
 */
class File
{
    /**
     * Get the file extension safely.
     *
     * @param string $path
     * @return string
     */
    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Get the file name without the extension.
     *
     * @param string $path
     * @return string
     */
    public static function name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Format a file size in bytes to a human-readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}