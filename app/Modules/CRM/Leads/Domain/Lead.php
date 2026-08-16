<?php
// Path: app/Modules/CRM/Leads/Domain/Lead.php

declare(strict_types=1);

namespace App\Modules\CRM\Leads\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Lead (CRM)
 * العميل المحتمل الذي لم يقم بأي عملية شراء بعد.
 */
class Lead extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'branch_id'    => 'integer',
        'first_name'   => 'string',
        'last_name'    => 'string',
        'company_name' => 'string',
        'email'        => 'string',
        'phone'        => 'string',
        'source'       => 'string', // e.g., 'website', 'referral', 'exhibition'
        'status'       => 'string', // 'new', 'contacted', 'qualified', 'converted', 'lost'
        'assigned_to'  => 'integer', // Sales Representative User ID
        'notes'        => 'string',
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];

    /**
     * الحصول على الاسم بالكامل.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return trim($this->getAttribute('first_name') . ' ' . $this->getAttribute('last_name'));
    }
}