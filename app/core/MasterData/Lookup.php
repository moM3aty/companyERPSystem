<?php
// Path: app/Core/MasterData/Lookup.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Generic Lookup
 * يمثل الجداول المرجعية العامة (Enums/Dropdowns) التي يديرها مدير النظام.
 * مثال: تصنيفات العملاء، أسباب المرتجعات، أنواع المصروفات.
 */
class Lookup extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'type'       => 'string', // e.g., 'customer_categories'
        'code'       => 'string',
        'value'      => 'string',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}