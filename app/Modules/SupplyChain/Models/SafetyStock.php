<?php
// Path: app/Modules/SupplyChain/Models/SafetyStock.php

declare(strict_types=1);

namespace App\Modules\SupplyChain\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Safety Stock
 * يمثل الرصيد الآمن المحسوب آلياً للصنف بناءً على فترات التوريد واستهلاك الطوارئ.
 */
class SafetyStock extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'product_id'          => 'integer',
        'warehouse_id'        => 'integer',
        'lead_time_days'      => 'integer', // الوقت المستغرق من طلب البضاعة لحين وصولها
        'calculated_min_qty'  => 'float',   // الكمية التي يجب أن لا ينزل الرصيد عنها
        'last_calculated_at'  => 'string',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}