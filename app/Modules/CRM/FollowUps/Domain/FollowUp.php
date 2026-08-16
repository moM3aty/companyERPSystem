<?php
// Path: app/Modules/CRM/FollowUps/Domain/FollowUp.php

declare(strict_types=1);

namespace App\Modules\CRM\FollowUps\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: CRM Follow-Up
 * يمثل موعد المتابعة لعميل أو فرصة بيعية. يتم قراءته من قبل موظف المبيعات لترتيب مهامه اليومية.
 */
class FollowUp extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'            => 'integer',
        'company_id'    => 'integer',
        'entity_type'   => 'string', // 'lead', 'opportunity', 'customer'
        'entity_id'     => 'integer',
        'assigned_to'   => 'integer', // مندوب المبيعات
        'scheduled_at'  => 'string', // YYYY-MM-DD HH:MM:SS
        'type'          => 'string', // 'call', 'email', 'meeting'
        'notes'         => 'string',
        'status'        => 'string', // 'pending', 'completed', 'cancelled'
        'completed_at'  => 'string',
        'created_by'    => 'integer',
        'created_at'    => 'string',
        'updated_at'  => 'string',
    ];

    public function isPending(): bool
    {
        return $this->getAttribute('status') === 'pending';
    }
}