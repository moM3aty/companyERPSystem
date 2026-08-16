<?php
// Path: app/Modules/AdvancedPricing/Models/CustomerPriceContract.php

declare(strict_types=1);

namespace App\Modules\AdvancedPricing\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Customer Price Contract
 * يمثل عقداً خاصاً لعميل (B2B) يضمن له سعراً ثابتاً لمنتج معين خلال فترة زمنية محددة.
 * هذا العقد يتجاوز جميع قوائم الأسعار والعروض الترويجية (أعلى أولوية في التسعير).
 */
class CustomerPriceContract extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'customer_id'  => 'integer',
        'product_id'   => 'integer',
        'agreed_price' => 'float',   // السعر المتفق عليه (ملزم)
        'min_quantity' => 'float',   // الحد الأدنى للكمية للحصول على السعر
        'valid_from'   => 'string',  // YYYY-MM-DD
        'valid_to'     => 'string',  // YYYY-MM-DD
        'is_active'    => 'boolean',
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];
}