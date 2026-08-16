<?php
// Path: app/Core/MasterData/Country.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Country
 * يمثل بيانات الدولة الأساسية (الاسم، كود ISO، كود الاتصال).
 */
class Country extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'code'       => 'string', // e.g., 'SA', 'EG', 'US'
        'name'       => 'string',
        'dial_code'  => 'string', // e.g., '+966', '+20'
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}