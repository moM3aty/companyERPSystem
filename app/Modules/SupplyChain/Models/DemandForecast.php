<?php
// Path: app/Modules/SupplyChain/Models/DemandForecast.php

declare(strict_types=1);

namespace App\Modules\SupplyChain\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Demand Forecast
 * التنبؤ بحجم الطلب المستقبلي للصنف بناءً على التحليل التاريخي للمبيعات لضمان توفره.
 */
class DemandForecast extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'product_id'        => 'integer',
        'forecast_period'   => 'string', // YYYY-MM
        'expected_quantity' => 'float',  // الكمية المتوقع بيعها
        'confidence_score'  => 'float',  // دقة التنبؤ المئوية (مثال: 85.5%)
        'algorithm_used'    => 'string', // 'moving_average', 'exponential_smoothing'
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}