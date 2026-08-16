<?php
// Path: app/Core/Helpers/functions.php

declare(strict_types=1);

if (!function_exists('env')) {
    /**
     * Get the value of an environment variable or return a default value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        // Check $_ENV, $_SERVER, and getenv()
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null) {
            return $default;
        }

        $lower = strtolower((string) $value);

        // Convert string booleans and nulls to actual types
        if ($lower === 'true') {
            return true;
        }
        if ($lower === 'false') {
            return false;
        }
        if ($lower === 'null') {
            return null;
        }

        return $value;
    }
}