<?php
// Path: app/Modules/POS/Orders/Domain/PosOrder.php

declare(strict_types=1);

namespace App\Modules\POS\Orders\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: POS Order
 * يمثل فاتورة المبيعات السريعة من نقطة البيع.
 */
class PosOrder extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'shift_id'         => 'integer',
        'customer_id'      => 'integer', // Nullable (مبيعات طياري)
        'order_number'     => 'string',
        'subtotal'         => 'float',
        'tax_total'        => 'float',
        'discount_total'   => 'float',
        'grand_total'      => 'float',
        'payment_method'   => 'string', // 'cash', 'card'
        'status'           => 'string', // 'completed', 'refunded'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}