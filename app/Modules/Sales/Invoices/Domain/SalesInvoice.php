<?php
// Path: app/Modules/Sales/Invoices/Domain/SalesInvoice.php

declare(strict_types=1);

namespace App\Modules\Sales\Invoices\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Sales Invoice
 * يمثل ترويسة فاتورة المبيعات بعد إتمام جميع العمليات الحسابية الآمنة.
 */
class SalesInvoice extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'invoice_no'       => 'string',
        'customer_id'      => 'integer',
        'invoice_date'     => 'string',
        'due_date'         => 'string',
        'currency_id'      => 'integer',
        'subtotal'         => 'float',
        'discount_total'   => 'float',
        'tax_total'        => 'float',
        'grand_total'      => 'float',
        'paid_amount'      => 'float',
        'status'           => 'string', // draft, approved, posted, paid, voided
        'journal_entry_id' => 'integer',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];
}