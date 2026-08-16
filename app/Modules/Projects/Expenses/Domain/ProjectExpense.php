<?php
// Path: app/Modules/Projects/Expenses/Domain/ProjectExpense.php

declare(strict_types=1);

namespace App\Modules\Projects\Expenses\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;

/**
 * Enterprise Domain Entity: Project Expense
 * يمثل مصروفاً مالياً مباشراً تم إنفاقه على المشروع (مثل فواتير المشتريات العينية أو تذاكر سفر).
 */
class ProjectExpense extends BaseModel
{
    use HasTenant, HasTimestamps;

    protected array $casts = [
        'id'           => 'integer',
        'company_id'   => 'integer',
        'project_id'   => 'integer',
        'task_id'      => 'integer', // Optional
        'employee_id'  => 'integer',
        'amount'       => 'float',
        'currency_id'  => 'integer',
        'description'  => 'string',
        'receipt_path' => 'string', // Path to the uploaded receipt/invoice
        'status'       => 'string', // 'pending', 'approved', 'rejected'
        'created_at'   => 'string',
        'updated_at'   => 'string',
    ];
}