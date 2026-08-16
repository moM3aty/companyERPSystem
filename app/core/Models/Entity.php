<?php
// Path: app/Core/Models/Entity.php

declare(strict_types=1);

namespace App\Core\Models;

use JsonSerializable;

/**
 * Enterprise Base Entity
 * Represents a Data Transfer Object (DTO) with automatic attribute casting and JSON serialization.
 */
abstract class Entity implements JsonSerializable
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The attributes that should be cast to native types.
     * Example: ['is_active' => 'boolean', 'settings' => 'json']
     *
     * @var array
     */
    protected array $casts = [];

    /**
     * Entity constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill the entity with an array of attributes.
     *
     * @param array $attributes
     * @return self
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Set a given attribute on the entity.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Get an attribute from the entity, applying any necessary casts.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute(string $key): mixed
    {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }

        return $this->castAttribute($key, $this->attributes[$key]);
    }

    /**
     * Cast an attribute to a native PHP type based on the $casts array.
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        if ($value === null || !array_key_exists($key, $this->casts)) {
            return $value;
        }

        return match (strtolower($this->casts[$key])) {
            'int', 'integer' => (int) $value,
            'real', 'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array', 'json' => is_string($value) ? json_decode($value, true) : (array) $value,
            default => $value,
        };
    }

    /**
     * Convert the entity's attributes to an array, applying casts.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->attributes as $key => $value) {
            $array[$key] = $this->getAttribute($key);
        }
        return $array;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Dynamically retrieve attributes on the entity.
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the entity.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if an attribute exists on the entity.
     *
     * @param string $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }
}