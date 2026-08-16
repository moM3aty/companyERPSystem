<?php
// Path: app/Modules/Fleet/Vehicles/Domain/Vehicle.php

declare(strict_types=1);

namespace App\Modules\Fleet\Vehicles\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasSoftDeletes;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Vehicle
 * يمثل المركبة/السيارة داخل أسطول الشركة.
 */
class Vehicle extends BaseModel
{
    use HasTenant, HasTimestamps, HasSoftDeletes, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'plate_number'     => 'string', // رقم اللوحة
        'make'             => 'string', // الشركة المصنعة (e.g., Toyota, Volvo)
        'model'            => 'string', // الموديل
        'year'             => 'integer',
        'chassis_number'   => 'string', // رقم الشاسيه (VIN)
        'driver_id'        => 'integer', // السائق الافتراضي (Employee ID)
        'asset_id'         => 'integer', // ربط السيارة بسجل الأصول الثابتة
        'status'           => 'string', // active, maintenance, out_of_service
        'current_mileage'  => 'float',  // قراءة العداد الحالية
        'created_at'       => 'string',
        'updated_at'       => 'string',
        'deleted_at'       => 'string',
    ];

    /**
     * التحقق مما إذا كانت المركبة متاحة للعمل.
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->getAttribute('status') === 'active';
    }
}