<?php
// Path: app/Core/Organization/Location.php

declare(strict_types=1);

namespace App\Core\Organization;

use App\Core\Models\Entity;

/**
 * Enterprise Location Entity
 * المواقع الجغرافية أو المباني التابعة للشركة (مهم لإدارة الأصول الثابتة والمخازن).
 */
class Location extends Entity
{
    protected array $casts = [
        'id' => 'integer',
        'company_id' => 'integer',
        'name' => 'string',
        'address' => 'string',
        'city' => 'string',
        'country_code' => 'string',
        'is_active' => 'boolean',
    ];
}