<?php
// Path: app/Core/MasterData/PaymentTerm.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Payment Term
 * يمثل شروط الدفع للعملاء والموردين (مثال: الدفع خلال 30 يوم - Net 30).
 */
class PaymentTerm extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'code'       => 'string', // e.g., 'NET30'
        'name'       => 'string',
        'days_due'   => 'integer',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}