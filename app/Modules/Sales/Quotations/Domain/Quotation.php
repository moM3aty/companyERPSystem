<?php
// Path: app/Modules/Sales/Quotations/Domain/Quotation.php

declare(strict_types=1);

namespace App\Modules\Sales\Quotations\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Sales Quotation
 * عرض السعر المقدم للعميل قبل تحوله إلى طلب مبيعات.
 */
class Quotation extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'quotation_no'     => 'string',
        'customer_id'      => 'integer',
        'quotation_date'   => 'string',
        'valid_until'      => 'string',
        'currency_id'      => 'integer',
        'subtotal'         => 'float',
        'discount_total'   => 'float',
        'tax_total'        => 'float',
        'grand_total'      => 'float',
        'status'           => 'string', // 'draft', 'sent', 'accepted', 'rejected', 'expired'
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}