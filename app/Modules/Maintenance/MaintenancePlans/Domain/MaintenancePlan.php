<?php
// Path: app/Modules/Maintenance/MaintenancePlans/Domain/MaintenancePlan.php

declare(strict_types=1);

namespace App\Modules\Maintenance\MaintenancePlans\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Maintenance Plan
 * يمثل خطة الصيانة الوقائية المجدولة لأصل معين (مثال: صيانة دورية كل 30 يوم).
 */
class MaintenancePlan extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'asset_id'         => 'integer', // الأصل المراد صيانته
        'name'             => 'string',
        'description'      => 'string',
        'frequency_days'   => 'integer', // معدل التكرار بالأيام
        'next_due_date'    => 'string',  // تاريخ الاستحقاق القادم
        'status'           => 'string',  // active, paused
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];

    public function isActive(): bool
    {
        return $this->getAttribute('status') === 'active';
    }
}