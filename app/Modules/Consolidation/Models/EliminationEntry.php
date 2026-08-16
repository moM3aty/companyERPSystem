<?php
// Path: app/Modules/Consolidation/Models/EliminationEntry.php

declare(strict_types=1);

namespace App\Modules\Consolidation\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Elimination Entry
 * قيود الاستبعاد. تستخدم لإلغاء الحركات المتبادلة بين شركات المجموعة 
 * (مثل ذمم دائنة لشركة أ يقابلها ذمم مدينة لشركة ب) لكي لا تتضخم ميزانية المجموعة بالوهم.
 */
class EliminationEntry extends BaseModel
{
    use HasTimestamps;

    protected array $casts = [
        'id'                     => 'integer',
        'consolidation_group_id' => 'integer',
        'period_year'            => 'integer',
        'period_month'           => 'integer',
        'from_company_id'        => 'integer',
        'to_company_id'          => 'integer',
        'account_id'             => 'integer', // حساب الـ GL الذي سيتم استبعاده
        'elimination_amount'     => 'float',
        'reason'                 => 'string',  // سبب الاستبعاد (e.g., Intercompany AR/AP)
        'created_at'             => 'string',
        'updated_at'             => 'string',
    ];
}