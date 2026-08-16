<?php
// Path: app/Modules/Treasury/Models/BankAccount.php

declare(strict_types=1);

namespace App\Modules\Treasury\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Bank Account
 * يمثل الحساب البنكي الفعلي للشركة داخل مؤسسة بنكية معينة، ويرتبط بدليل الحسابات (GL).
 */
class BankAccount extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'bank_id'         => 'integer',
        'account_name'    => 'string', // اسم الحساب (مثال: حساب العمليات التشغيلية)
        'account_number'  => 'string',
        'iban'            => 'string',
        'currency_id'     => 'integer',
        'gl_account_id'   => 'integer', // حساب الأستاذ العام
        'current_balance' => 'float',   // رصيد دفتر الأستاذ (Denormalized للسرعة)
        'is_active'       => 'boolean',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}