<?php
// Path: app/Modules/Fleet/Trips/Domain/Trip.php

declare(strict_types=1);

namespace App\Modules\Fleet\Trips\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Fleet Trip
 * يمثل رحلة قامت بها مركبة معينة (لتتبع الاستهلاك والتكاليف).
 */
class Trip extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                => 'integer',
        'company_id'        => 'integer',
        'vehicle_id'        => 'integer',
        'driver_id'         => 'integer',
        'start_location'    => 'string',
        'end_location'      => 'string',
        'start_time'        => 'string',
        'end_time'          => 'string',
        'distance_covered'  => 'float',
        'fuel_consumed'     => 'float',
        'trip_cost'         => 'float', // إجمالي تكاليف الرحلة (وقود، رسوم عبور)
        'status'            => 'string', // scheduled, in_progress, completed, cancelled
        'created_by'        => 'integer',
        'created_at'        => 'string',
        'updated_at'        => 'string',
    ];

    /**
     * التحقق مما إذا كانت الرحلة مكتملة.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->getAttribute('status') === 'completed';
    }
}