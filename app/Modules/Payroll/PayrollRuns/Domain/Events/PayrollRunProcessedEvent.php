<?php
// Path: app/Modules/Payroll/PayrollRuns/Domain/Events/PayrollRunProcessedEvent.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Domain\Events;

use App\Core\Events\DomainEvent;

/**
 * Enterprise Domain Event: Payroll Run Processed
 * يتم إطلاقه فور انتهاء مسير الرواتب ليقوم الـ General Ledger بتسجيل قيد الرواتب،
 * ويقوم موديول الـ Notifications بإبلاغ الموظفين بصدور قسائم رواتبهم.
 */
class PayrollRunProcessedEvent extends DomainEvent
{
    public readonly int $companyId;
    public readonly string $runPeriod;
    public readonly float $netTotal;

    public function __construct(int $payrollRunId, int $companyId, string $runPeriod, float $netTotal)
    {
        parent::__construct($payrollRunId);
        $this->companyId = $companyId;
        $this->runPeriod = $runPeriod;
        $this->netTotal = $netTotal;
    }

    public function toPayload(): array
    {
        return array_merge(parent::toPayload(), [
            'company_id' => $this->companyId,
            'run_period' => $this->runPeriod,
            'net_total'  => $this->netTotal,
        ]);
    }
}