<?php
// Path: app/Modules/Purchasing/RFQ/Domain/Rfq.php

declare(strict_types=1);

namespace App\Modules\Purchasing\RFQ\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Request For Quotation (RFQ)
 * يمثل طلب تسعير يتم إرساله لعدة موردين للحصول على أفضل سعر قبل الشراء.
 */
class Rfq extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'rfq_number'       => 'string',
        'title'            => 'string',
        'deadline_date'    => 'string', // آخر موعد لاستلام العروض من الموردين
        'delivery_date'    => 'string', // الموعد المتوقع لتسليم البضاعة
        'status'           => 'string', // 'draft', 'sent', 'bidding', 'completed', 'cancelled'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}