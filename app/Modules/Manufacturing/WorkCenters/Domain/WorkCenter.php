<?php
// Path: app/Modules/Manufacturing/WorkCenters/Domain/WorkCenter.php

declare(strict_types=1);

namespace App\Modules\Manufacturing\WorkCenters\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Work Center
 * يمثل مركز العمل (ماكينة، خط تجميع، أو مجموعة عمال).
 * يستخدم لحساب تكلفة التشغيل بالساعة وجدولة الإنتاج.
 */
class WorkCenter extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'code'             => 'string', // e.g., 'WC-CNC-01'
        'name'             => 'string',
        'type'             => 'string', // 'machine', 'human', 'mixed'
        'cost_per_hour'    => 'float',  // التكلفة التشغيلية في الساعة (كهرباء + عمالة + إهلاك)
        'capacity_per_day' => 'float',  // الطاقة الاستيعابية القصوى في اليوم (بالساعات)
        'is_active'        => 'boolean',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}