<?php
// Path: app/Core/FeatureFlags/FeatureRule.php

declare(strict_types=1);

namespace App\Core\FeatureFlags;

use App\Core\Models\Entity;

/**
 * Enterprise Feature Rule Entity
 * يمثل شروط تفعيل الميزة لشركات أو مستخدمين معينين (Targeting).
 */
class FeatureRule extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'feature_id' => 'integer',
        'target_type' => 'string', // 'company', 'user', 'branch'
        'target_id' => 'integer',
        'is_enabled' => 'boolean',
    ];
}