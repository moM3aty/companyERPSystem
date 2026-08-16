<?php
// Path: app/Core/MasterData/Tax.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Tax
 * يمثل نوع الضريبة (مثال: ضريبة القيمة المضافة VAT، أو ضريبة الخصم والإضافة).
 */
class Tax extends Entity
{
    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'code'        => 'string', // e.g., 'VAT'
        'name'        => 'string',
        'type'        => 'string', // 'percentage', 'fixed'
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}