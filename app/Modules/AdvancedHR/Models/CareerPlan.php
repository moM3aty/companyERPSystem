<?php
// File 6: app/Modules/AdvancedHR/Models/CareerPlan.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Career Plan
 * يمثل خطة المسار الوظيفي للموظف (من منصبه الحالي إلى منصبه المستهدف).
 */
class CareerPlan extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                 => 'integer',
        'company_id'         => 'integer',
        'employee_id'        => 'integer',
        'current_position_id'=> 'integer',
        'target_position_id' => 'integer',
        'readiness_timeframe'=> 'string', // 'immediate', '1_to_3_years', '3_to_5_years'
        'status'             => 'string', // 'active', 'achieved', 'cancelled'
        'created_at'         => 'string',
        'updated_at'         => 'string',
    ];
}