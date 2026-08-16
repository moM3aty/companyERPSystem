<?php
// Path: app/Modules/HR/EmployeeSelfService/Domain/ExpenseClaimItem.php

declare(strict_types=1);

namespace App\Modules\HR\EmployeeSelfService\Domain;

use App\Core\Models\Entity;

/**
 * Enterprise Domain Entity: Expense Claim Item
 * الفواتير الفردية داخل مطالبة الموظف.
 */
class ExpenseClaimItem extends Entity
{
    protected array $casts = [
        'id'               => 'integer',
        'expense_claim_id' => 'integer',
        'expense_type'     => 'string', // 'travel', 'meals', 'supplies'
        'receipt_date'     => 'string',
        'amount'           => 'float',
        'description'      => 'string',
        'attachment_path'  => 'string', // مسار الفاتورة المرفقة
    ];
}