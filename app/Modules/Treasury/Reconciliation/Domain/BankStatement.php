<?php
// Path: app/Modules/Treasury/Reconciliation/Domain/BankStatement.php

declare(strict_types=1);

namespace App\Modules\Treasury\Reconciliation\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Bank Statement
 * يمثل كشف الحساب البنكي الذي تم رفعه أو إدخاله للنظام لغرض المطابقة.
 */
class BankStatement extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'                  => 'integer',
        'company_id'          => 'integer',
        'treasury_account_id' => 'integer', // حساب البنك في نظامنا
        'statement_date'      => 'string',  // YYYY-MM-DD
        'statement_reference' => 'string',
        'opening_balance'     => 'float',
        'closing_balance'     => 'float',
        'status'              => 'string',  // 'draft', 'reconciled'
        'created_by'          => 'integer',
        'created_at'          => 'string',
        'updated_at'          => 'string',
    ];

    public function isReconciled(): bool
    {
        return $this->getAttribute('status') === 'reconciled';
    }
}