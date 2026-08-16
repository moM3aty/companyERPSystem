<?php
// Path: app/Core/MasterData/Region.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Region
 * يمثل المنطقة أو المحافظة التابعة لدولة معينة (مهم للضرائب الإقليمية).
 */
class Region extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'country_id' => 'integer',
        'code'       => 'string',
        'name'       => 'string',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}