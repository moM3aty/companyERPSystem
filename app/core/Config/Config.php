<?php
// Path: app/Core/Config/Config.php

declare(strict_types=1);

namespace App\Core\Config;

/**
 * Enterprise Configuration Manager
 * Handles multi-dimensional configuration arrays with dot-notation access.
 */
class Config
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Determine if the given configuration value exists.
     * Supports dot notation (e.g., 'database.mysql.host').
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $array = $this->items;

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
     * Get the specified configuration value.
     * Supports dot notation.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $array = $this->items;

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Set a given configuration value.
     * Supports dot notation.
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function set(string|array $key, mixed $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];

        foreach ($keys as $innerKey => $innerValue) {
            $array = &$this->items;
            $segments = explode('.', $innerKey);

            while (count($segments) > 1) {
                $segment = array_shift($segments);

                if (! isset($array[$segment]) || ! is_array($array[$segment])) {
                    $array[$segment] = [];
                }

                $array = &$array[$segment];
            }

            $array[array_shift($segments)] = $innerValue;
        }
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Load a configuration file into a specific key prefix.
     *
     * @param string $prefix
     * @param string $path
     * @return void
     */
    public function loadFromFile(string $prefix, string $path): void
    {
        if (file_exists($path)) {
            $config = require $path;
            if (is_array($config)) {
                $this->set($prefix, $config);
            }
        }
    }
}