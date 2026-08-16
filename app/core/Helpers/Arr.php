<?php
// Path: app/Core/Helpers/Arr.php

declare(strict_types=1);

namespace App\Core\Helpers;

/**
 * Enterprise Array Helper
 * Provides robust utilities for manipulating arrays, especially useful for complex ERP data structures.
 */
class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array $array
     * @param string|int|null $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(array $array, string|int|null $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (!str_contains((string) $key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     *
     * @param array $array
     * @param string $key
     * @return bool
     */
    public static function has(array $array, string $key): bool
    {
        if (empty($array)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Pluck an array of values from an array of arrays/objects.
     *
     * @param array $array
     * @param string $valueKey
     * @param string|null $indexKey
     * @return array
     */
    public static function pluck(array $array, string $valueKey, ?string $indexKey = null): array
    {
        $results = [];

        foreach ($array as $item) {
            $itemValue = is_object($item) ? ($item->{$valueKey} ?? null) : (is_array($item) ? ($item[$valueKey] ?? null) : null);

            if (is_null($indexKey)) {
                $results[] = $itemValue;
            } else {
                $itemKey = is_object($item) ? ($item->{$indexKey} ?? null) : (is_array($item) ? ($item[$indexKey] ?? null) : null);
                if (!is_null($itemKey)) {
                    $results[$itemKey] = $itemValue;
                }
            }
        }

        return $results;
    }

    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    public static function except(array $array, array|string $keys): array
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    public static function only(array $array, array|string $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}