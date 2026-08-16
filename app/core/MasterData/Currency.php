<?php
// Path: app/Core/MasterData/Currency.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Currency
 * يمثل العملات المدعومة في النظام. غالباً ما تكون مشتركة لجميع الشركات (Global).
 */
class Currency extends Entity
{
    protected array $casts = [
        'id'            => 'integer',
        'code'          => 'string', // e.g., 'USD', 'SAR'
        'name'          => 'string',
        'symbol'        => 'string', // e.g., '$', 'ر.س'
        'exchange_rate' => 'float',  // Default or latest rate
        'is_base'       => 'boolean',
        'is_active'     => 'boolean',
        'created_at'    => 'string',
        'updated_at'    => 'string',
    ];
}