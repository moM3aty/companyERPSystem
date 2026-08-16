<?php
// Path: app/Core/MasterData/City.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: City
 * يمثل المدينة التابعة لمنطقة معينة.
 */
class City extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'region_id'  => 'integer',
        'name'       => 'string',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}