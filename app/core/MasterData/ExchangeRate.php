<?php
// Path: app/Core/MasterData/ExchangeRate.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Exchange Rate
 * يمثل سعر الصرف اللحظي أو التاريخي بين عملتين. مهم جداً لتقييم الفواتير القديمة.
 */
class ExchangeRate extends Entity
{
    protected array $casts = [
        'id'                 => 'integer',
        'company_id'         => 'integer',
        'base_currency_id'   => 'integer',
        'target_currency_id' => 'integer',
        'rate'               => 'float',
        'effective_date'     => 'string', // YYYY-MM-DD
        'created_at'         => 'string',
        'updated_at'         => 'string',
    ];
}