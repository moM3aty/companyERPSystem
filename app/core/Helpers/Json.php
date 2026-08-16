<?php
// Path: app/Core/Helpers/Json.php

declare(strict_types=1);

namespace App\Core\Helpers;

use JsonException;

/**
 * Enterprise JSON Helper
 * Wraps PHP's native JSON functions to throw strict exceptions on failure
 * rather than failing silently, preventing corrupted data in API responses.
 */
class Json
{
    /**
     * Encode a value to JSON securely.
     *
     * @param mixed $value
     * @param int $options
     * @param int $depth
     * @return string
     * @throws JsonException
     */
    public static function encode(mixed $value, int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES, int $depth = 512): string
    {
        $json = json_encode($value, $options | JSON_THROW_ON_ERROR, $depth);
        
        return $json;
    }

    /**
     * Decode a JSON string to a PHP array or object securely.
     *
     * @param string $json
     * @param bool $associative Default to true to return arrays instead of stdClass
     * @param int $depth
     * @param int $options
     * @return mixed
     * @throws JsonException
     */
    public static function decode(string $json, bool $associative = true, int $depth = 512, int $options = JSON_THROW_ON_ERROR): mixed
    {
        if (trim($json) === '') {
            return $associative ? [] : null;
        }

        return json_decode($json, $associative, $depth, $options);
    }

    /**
     * Check if a string is a valid JSON.
     *
     * @param string $json
     * @return bool
     */
    public static function isValid(string $json): bool
    {
        if (!is_string($json) || trim($json) === '') {
            return false;
        }

        try {
            json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            return true;
        } catch (JsonException $e) {
            return false;
        }
    }
}