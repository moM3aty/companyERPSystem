<?php
// Path: app/Modules/CRM/Models/Campaign.php

declare(strict_types=1);

namespace App\Modules\CRM\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise CRM Model: Campaign
 * يمثل حملة تسويقية تهدف لجلب Leads أو تفعيل العملاء الحاليين.
 */
class Campaign extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'name'             => 'string',
        'type'             => 'string', // 'email', 'social_media', 'event', 'telemarketing'
        'status'           => 'string', // 'planned', 'active', 'completed', 'cancelled'
        'start_date'       => 'string', // YYYY-MM-DD
        'end_date'         => 'string', // YYYY-MM-DD
        'budget'           => 'float',
        'expected_revenue' => 'float',
        'actual_revenue'   => 'float',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}