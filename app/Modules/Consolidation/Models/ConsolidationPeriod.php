<?php
// File 7: app/Modules/Consolidation/Models/ConsolidationPeriod.php
declare(strict_types=1);

namespace App\Modules\Consolidation\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Consolidation Period
 * يمثل الفترة المالية التي يتم فيها تجميع قوائم الشركات التابعة لإنتاج ميزانية مجمعة للمجموعة.
 */
class ConsolidationPeriod extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'                     => 'integer',
        'consolidation_group_id' => 'integer',
        'period_name'            => 'string', // e.g., 'Q1-2026'
        'start_date'             => 'string',
        'end_date'               => 'string',
        'status'                 => 'string', // 'open', 'translating', 'eliminated', 'closed'
        'created_by'             => 'integer',
        'created_at'             => 'string',
        'updated_at'             => 'string',
    ];
}