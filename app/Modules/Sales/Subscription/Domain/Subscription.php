<?php
// Path: app/Modules/Sales/Subscription/Domain/Subscription.php

declare(strict_types=1);

namespace App\Modules\Sales\Subscription\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Subscription
 * يمثل عقداً لفوترة دورية للعميل (مثال: صيانة سنوية، استضافة شهرية).
 */
class Subscription extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'customer_id'       => 'integer',
        'product_id'        => 'integer', // الخدمة المباعة
        'billing_cycle'     => 'string', // 'monthly', 'quarterly', 'yearly'
        'price'             => 'float',
        'currency_id'       => 'integer',
        'next_billing_date' => 'string', // YYYY-MM-DD
        'end_date'          => 'string', // YYYY-MM-DD
        'status'            => 'string', // 'active', 'suspended', 'cancelled'
        'created_by'        => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}