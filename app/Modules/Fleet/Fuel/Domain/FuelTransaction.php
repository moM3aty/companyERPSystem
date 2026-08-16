<?php
// Path: app/Modules/Fleet/Fuel/Domain/FuelTransaction.php

declare(strict_types=1);

namespace App\Modules\Fleet\Fuel\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Fuel Transaction
 * يوثق استهلاك الوقود لكل سيارة، ويرتبط بقراءة العداد لاحتساب كفاءة الاستهلاك (KM/Liter).
 */
class FuelTransaction extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'vehicle_id'        => 'integer',
        'driver_id'         => 'integer',
        'transaction_date'  => 'string', // YYYY-MM-DD
        'liters'            => 'float',
        'cost_per_liter'    => 'float',
        'total_cost'        => 'float',
        'odometer_reading'  => 'float',  // قراءة العداد وقت التعبئة
        'invoice_reference' => 'string', // رقم فاتورة المحطة
        'created_by'        => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];
}