<?php
// Path: app/Modules/AdvancedHR/Models/PerformanceReview.php
declare(strict_types=1);

namespace App\Modules\AdvancedHR\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Performance Review
 * تقييم الأداء النهائي للموظف خلال دورة تقييم معينة.
 */
class PerformanceReview extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                   => 'integer',
        'company_id'           => 'integer',
        'performance_cycle_id' => 'integer',
        'employee_id'          => 'integer',
        'reviewer_id'          => 'integer', // المدير المباشر أو المقيم
        'overall_score'        => 'float',   // من 100
        'rating_grade'         => 'string',  // 'Excellent', 'Good', 'Needs Improvement'
        'comments'             => 'string',
        'status'               => 'string',  // 'draft', 'submitted', 'approved'
        'created_at'           => 'string',
        'updated_at'           => 'string',
    ];
}