<?php
// Path: app/Core/Models/BaseModel.php

declare(strict_types=1);

namespace App\Core\Models;

/**
 * Enterprise Base Model
 * Extends the Entity DTO to include state tracking (Dirty vs Original values).
 * Highly critical for Audit Trails and partial Database Updates.
 */
abstract class BaseModel extends Entity
{
    /**
     * The model's original attributes before any modifications.
     *
     * @var array
     */
    protected array $original = [];

    /**
     * BaseModel constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->syncOriginal();
    }

    /**
     * Sync the original attributes with the current attributes.
     * This establishes the baseline for tracking future changes.
     *
     * @return self
     */
    public function syncOriginal(): self
    {
        $this->original = $this->attributes;
        
        return $this;
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty(): array
    {
        $dirty = [];

        foreach ($this->attributes as $key => $value) {
            if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
                $dirty[$key] = $value;
            }
        }

        return $dirty;
    }

    /**
     * Determine if the model or a given attribute has been modified.
     *
     * @param string|null $attribute
     * @return bool
     */
    public function isDirty(?string $attribute = null): bool
    {
        if ($attribute !== null) {
            return array_key_exists($attribute, $this->attributes) &&
                   (!array_key_exists($attribute, $this->original) || $this->attributes[$attribute] !== $this->original[$attribute]);
        }

        return count($this->getDirty()) > 0;
    }

    /**
     * Get the model's original attribute values.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function getOriginal(?string $key = null, mixed $default = null): mixed
    {
        if ($key !== null) {
            return $this->original[$key] ?? $default;
        }

        return $this->original;
    }
}