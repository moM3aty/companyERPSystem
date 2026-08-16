<?php
// Path: app/Modules/Treasury/Models/CashAccount.php

declare(strict_types=1);

namespace App\Modules\Treasury\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Cash Account
 * يمثل صناديق الخزينة الفيزيائية في فروع الشركة أو عهد الموظفين (Petty Cash).
 */
class CashAccount extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'              => 'integer',
        'company_id'      => 'integer',
        'branch_id'       => 'integer',
        'name'            => 'string', // مثال: صندوق المعرض الرئيسي
        'gl_account_id'   => 'integer',
        'custodian_id'    => 'integer', // الموظف المسؤول عن العهدة
        'current_balance' => 'float',
        'is_active'       => 'boolean',
        'created_at'      => 'string',
        'updated_at'      => 'string',
    ];
}