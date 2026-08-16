<?php
// Path: app/Modules/Treasury/Payments/Domain/PaymentVoucher.php

declare(strict_types=1);

namespace App\Modules\Treasury\Payments\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Payment Voucher (سند صرف)
 * يمثل عملية خروج أموال من الخزينة أو البنك لدفع التزامات (موردين، مصروفات، رواتب).
 */
class PaymentVoucher extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'voucher_no'          => 'string',
        'voucher_date'        => 'string',
        'treasury_account_id' => 'integer', // الخزينة أو البنك الذي سيتم الدفع منه (الدائن)
        'debit_account_id'    => 'integer', // الحساب المدين (مثلاً حساب المورد أو المصروف في GL)
        'amount'              => 'float',
        'currency_id'         => 'integer',
        'exchange_rate'       => 'float',
        'reference'           => 'string',  // رقم الشيك أو الحوالة البنكية
        'description'         => 'string',
        'journal_entry_id'    => 'integer', // القيد المحاسبي المولد آلياً
        'status'              => 'string',  // 'draft', 'posted', 'voided'
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}