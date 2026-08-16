<?php
// Path: app/Modules/CRM/Activities/Domain/Activity.php

declare(strict_types=1);

namespace App\Modules\CRM\Activities\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: CRM Activity
 * يمثل نشاطاً قام به مندوب المبيعات (اتصال، إيميل، اجتماع) مع عميل أو عميل محتمل.
 */
class Activity extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'type'             => 'string', // 'call', 'email', 'meeting', 'task'
        'related_to_type'  => 'string', // 'lead', 'customer', 'opportunity'
        'related_to_id'    => 'integer',
        'subject'          => 'string',
        'description'      => 'string',
        'scheduled_at'     => 'string', // متى سيتم النشاط أو متى تم YYYY-MM-DD HH:MM:SS
        'status'           => 'string', // 'pending', 'completed', 'cancelled'
        'assigned_to'      => 'integer', // User ID (Sales Rep)
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}