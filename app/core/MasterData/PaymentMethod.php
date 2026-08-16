<?php
// Path: app/Core/MasterData/PaymentMethod.php

declare(strict_types=1);

namespace App\Core\MasterData;

use App\Core\Models\Entity;

/**
 * Enterprise Master Data: Payment Method
 * يمثل طرق الدفع (نقدي، تحويل بنكي، فيزا).
 */
class PaymentMethod extends Entity
{
    protected array $casts = [
        'id'         => 'integer',
        'company_id' => 'integer',
        'code'       => 'string', // e.g., 'CASH', 'BANK_TRANSFER'
        'name'       => 'string',
        'is_active'  => 'boolean',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];
}