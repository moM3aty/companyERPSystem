<?php
// Path: app/Modules/Payroll/PayrollRuns/Domain/PayrollRunRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Payroll Run
 */
interface PayrollRunRepositoryInterface extends RepositoryInterface
{
    /**
     * التحقق مما إذا كان تم تشغيل مسير رواتب لهذا الشهر مسبقاً لمنع الازدواجية.
     *
     * @param string $runPeriod
     * @param int $companyId
     * @return bool
     */
    public function existsForPeriod(string $runPeriod, int $companyId): bool;
}