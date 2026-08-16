<?php
// Path: app/Modules/Sales/CustomerPayments/Domain/InvoiceAllocation.php

declare(strict_types=1);

namespace App\Modules\Sales\CustomerPayments\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Invoice Allocation (Settlement)
 * يمثل عملية تسوية (تخصيص) جزء من أو كل مبلغ سند القبض لصالح فاتورة مبيعات محددة،
 * لخفض مديونية العميل (AR) وإغلاق الفاتورة.
 */
class InvoiceAllocation extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'receipt_id'       => 'integer', // سند القبض (من موديول الخزينة)
        'sales_invoice_id' => 'integer', // الفاتورة المراد تسديدها
        'allocated_amount' => 'float',
        'allocated_by'     => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}