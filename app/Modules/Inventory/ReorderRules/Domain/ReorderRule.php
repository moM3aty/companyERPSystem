<?php
// Path: app/Modules/Inventory/ReorderRules/Domain/ReorderRule.php

declare(strict_types=1);

namespace App\Modules\Inventory\ReorderRules\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;

/**
 * Enterprise Domain Entity: Reorder Rule (Min/Max Planning)
 * قواعد إعادة الطلب: يحدد الحد الأدنى والأقصى للمخزون لكل صنف في كل مستودع.
 */
class ReorderRule extends BaseModel
{
    use HasTenant;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'product_id'       => 'integer',
        'warehouse_id'     => 'integer',
        'min_quantity'     => 'float', // نقطة إعادة الطلب (Reorder Point)
        'max_quantity'     => 'float', // الحد الأقصى للمخزون (Target Stock)
        'lead_time_days'   => 'integer', // وقت التوريد المتوقع بالأيام
        'is_active'        => 'boolean',
    ];
}