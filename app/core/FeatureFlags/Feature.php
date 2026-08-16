<?php
// Path: app/Core/FeatureFlags/Feature.php

declare(strict_types=1);

namespace App\Core\FeatureFlags;

use App\Core\Models\Entity;

/**
 * Enterprise Feature Entity
 * يمثل الميزة التي يمكن تفعيلها أو تعطيلها في النظام.
 */
class Feature extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * التحقق مما إذا كانت الميزة مفعلة بشكل عام لكل النظام.
     *
     * @return bool
     */
    public function isGloballyEnabled(): bool
    {
        return $this->getAttribute('is_global') === true && $this->getAttribute('is_active') === true;
    }
}