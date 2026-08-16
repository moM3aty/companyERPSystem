<?php
// Path: app/Modules/Treasury/Models/BankReconciliation.php

declare(strict_types=1);

namespace App\Modules\Treasury\Models;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Model: Bank Reconciliation
 * يمثل ترويسة كشف التسوية البنكية الذي يطابق حسابات الشركة مع البنك.
 */
class BankReconciliation extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'bank_account_id'     => 'integer',
        'statement_date'      => 'string',
        'statement_balance'   => 'float', // رصيد كشف البنك
        'system_balance'      => 'float', // رصيد الدفاتر (System)
        'difference'          => 'float',
        'status'              => 'string', // 'draft', 'reconciled'
        'reconciled_by'       => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];
}