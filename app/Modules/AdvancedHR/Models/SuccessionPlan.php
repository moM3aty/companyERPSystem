<?php
// Path: app/Modules/AdvancedHR/Models/SuccessionPlan.php

declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Succession Plan
 * خطة التعاقب الوظيفي: تحدد من هو الموظف المؤهل لاستلام منصب حيوي في حال خلوه فجأة.
 */
class SuccessionPlan extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                     => 'integer',
        'company_id'             => 'integer',
        'key_position_id'        => 'integer', // المنصب الحرج (مثال: CFO)
        'potential_successor_id' => 'integer', // الموظف المرشح للخلافة
        'readiness_level'        => 'string',  // 'ready_now', 'ready_1_year', 'ready_3_years'
        'notes'                  => 'string',
        'status'                 => 'string',  // 'active', 'archived'
        'created_at'             => 'string',
        'updated_at'             => 'string',
    ];
}