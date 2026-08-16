<?php
// Path: app/Modules/Maintenance/WorkOrders/Domain/WorkOrder.php

declare(strict_types=1);

namespace App\Modules\Maintenance\WorkOrders\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Work Order
 * يمثل أمر العمل الفعلي (صيانة تصحيحية طارئة، أو صيانة وقائية ناتجة عن خطة).
 */
class WorkOrder extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'work_order_number'   => 'string',
        'maintenance_plan_id' => 'integer', // قد يكون Null إذا كانت الصيانة طارئة (Corrective)
        'asset_id'            => 'integer',
        'title'               => 'string',
        'description'         => 'string',
        'assigned_to'         => 'integer', // فني الصيانة (Employee ID)
        'priority'            => 'string',  // low, normal, high, critical
        'status'              => 'string',  // pending, in_progress, completed, cancelled
        'scheduled_date'      => 'string',
        'completed_at'        => 'string',
        'estimated_cost'      => 'float',
        'actual_cost'         => 'float',
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}