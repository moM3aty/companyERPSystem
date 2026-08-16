<?php
// Path: app/Modules/Sales/Commission/Domain/CommissionRecord.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Commission Record
 * السجل المالي الذي يثبت استحقاق عمولة لمندوب مبيعات على فاتورة معينة.
 */
class CommissionRecord extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'                 => 'integer',
        'company_id'         => 'integer',
        'employee_id'        => 'integer', // مندوب المبيعات
        'commission_plan_id' => 'integer',
        'sales_invoice_id'   => 'integer',
        'invoice_amount'     => 'float',
        'commission_amount'  => 'float',
        'status'             => 'string', // 'pending', 'paid', 'voided'
        'created_at'         => 'string',
        'updated_at'         => 'string',
    ];
}