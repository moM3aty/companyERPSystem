<?php
// Path: app/Modules/Sales/Commission/Domain/CommissionPlan.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Commission Plan
 * يمثل خطة العمولات لمندوبي المبيعات.
 */
class CommissionPlan extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'             => 'integer',
        'company_id'     => 'integer',
        'name'           => 'string',
        'type'           => 'string', // 'percentage', 'fixed_per_invoice'
        'value'          => 'float',
        'target_amount'  => 'float', // التارجت المطلوب لتحقيق العمولة (0 = بدون تارجت)
        'is_active'      => 'boolean',
        'created_at'     => 'string',
        'updated_at'     => 'string',
    ];
}