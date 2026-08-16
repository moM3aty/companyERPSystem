<?php
// Path: app/Modules/Purchasing/Invoices/Domain/PurchaseInvoice.php

declare(strict_types=1);

namespace App\Modules\Purchasing\Invoices\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Purchase Invoice (Bill)
 * يمثل فاتورة المشتريات (مطالبة المورد).
 */
class PurchaseInvoice extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'branch_id'        => 'integer',
        'invoice_no'       => 'string', // الرقم التسلسلي الداخلي
        'supplier_bill_no' => 'string', // رقم الفاتورة كما صدر من المورد (مهم للضرائب)
        'supplier_id'      => 'integer',
        'purchase_order_id'=> 'integer', // إن وجدت
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