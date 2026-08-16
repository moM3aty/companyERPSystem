<?php
// Path: app/Core/Organization/Organization.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Models\Entity;

/**
 * Enterprise Organization Entity
 * يعبر عن الإعدادات الهيكلية لمنظمة/شركة بشكل عام (مثل: نوع الهيكل هل هو Matrix أو Flat).
 */
class Organization extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'structure_type' => 'string', // 'hierarchical', 'matrix', 'flat'
        'max_depth' => 'integer',
    ];
}