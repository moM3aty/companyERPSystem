<?php
// Path: app/Core/MasterData/TaxRate.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Tax Rate
 * يمثل النسبة الفعلية للضريبة خلال فترة زمنية معينة (مثال: الـ VAT كانت 5% ثم أصبحت 15%).
 */
class TaxRate extends Entity
{
    protected array $casts = [
        'id'             => 'integer',
        'tax_id'         => 'integer',
        'rate'           => 'float',
        'effective_from' => 'string', // YYYY-MM-DD
        'effective_to'   => 'string', // YYYY-MM-DD
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}