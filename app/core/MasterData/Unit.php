<?php
// Path: app/Core/MasterData/Unit.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Unit of Measurement (UOM)
 * يمثل وحدات القياس (كجم، حبة، لتر) وتدعم التحويلات (مثلاً الكرتونة = 12 حبة).
 */
class Unit extends Entity
{
    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'code'              => 'string', // e.g., 'KG', 'BOX'
        'name'              => 'string',
        'type'              => 'string', // weight, volume, length, piece
        'base_unit_id'      => 'integer',
        'conversion_factor' => 'float',
        'is_active'         => 'boolean',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}