<?php
// Path: app/Modules/Payroll/PayrollRuns/Domain/PayrollRun.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Domain;

use App\Core\Models\BaseModel;
use App\Core\Models\Traits\HasTenant;
use App\Core\Models\Traits\HasTimestamps;
use App\Core\Models\Traits\HasAudit;

/**
 * Enterprise Domain Entity: Payroll Run
 * يمثل مسير الرواتب المجمع لشهر معين (مثال: مسير رواتب شهر 08-2026).
 */
class PayrollRun extends BaseModel
{
    use HasTenant, HasTimestamps, HasAudit;

    protected array $casts = [
        'id'               => 'integer',
        'company_id'       => 'integer',
        'run_reference'    => 'string', // e.g., 'PR-2026-08'
        'run_period'       => 'string', // YYYY-MM
        'total_basic'      => 'float',
        'total_allowances' => 'float',
        'total_deductions' => 'float',
        'net_total'        => 'float',
        'status'           => 'string', // 'draft', 'approved', 'posted'
        'journal_entry_id' => 'integer',
        'created_by'       => 'integer',
        'created_at'       => 'string',
        'updated_at'       => 'string',
    ];

    /**
     * التحقق مما إذا كان المسير مرحلاً ونهائياً.
     *
     * @return bool
     */
    public function isPosted(): bool
    {
        return $this->getAttribute('status') === 'posted';
    }
}