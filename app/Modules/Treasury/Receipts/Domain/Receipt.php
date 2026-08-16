<?php
// Path: app/Modules/Treasury/Receipts/Domain/Receipt.php

declare(strict_types=1);

namespace App\Modules\Treasury\Receipts\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Receipt (سند قبض)
 * يمثل عملية استلام أموال وإيداعها في الخزينة أو البنك.
 */
class Receipt extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'receipt_no'          => 'string',
        'receipt_date'        => 'string',
        'treasury_account_id' => 'integer', // الخزينة أو البنك المستلم
        'credit_account_id'   => 'integer', // الحساب الدائن (مثلاً حساب العميل في GL)
        'amount'              => 'float',
        'currency_id'         => 'integer',
        'exchange_rate'       => 'float',
        'reference'           => 'string',  // رقم شيك أو حوالة
        'description'         => 'string',
        'journal_entry_id'    => 'integer', // القيد المحاسبي المولد آلياً
        'status'              => 'string',  // 'draft', 'posted', 'voided'
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}