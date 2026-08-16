<?php
// File 7: app/Modules/AdvancedHR/Models/PerformanceCycle.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Performance Cycle
 * يمثل الدورة الزمنية لتقييم الأداء (مثلاً: التقييم السنوي لعام 2026).
 */
class PerformanceCycle extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'          => 'integer',
        'company_id'  => 'integer',
        'name'        => 'string', // e.g., 'Annual Performance Review 2026'
        'start_date'  => 'string',
        'end_date'    => 'string',
        'is_active'   => 'boolean',
        'created_at'  => 'string',
        'updated_at'  => 'string',
    ];
}