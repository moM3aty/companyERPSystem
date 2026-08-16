<?php
// Path: app/Modules/Payroll/Payslips/Domain/PayslipRepositoryInterface.php

declare(strict_types=1);

namespace App\Modules\Payroll\Payslips\Domain;

use App\Core\Contracts\RepositoryInterface;

/**
 * Enterprise Repository Interface: Payslip
 */
interface PayslipRepositoryInterface extends RepositoryInterface
{
    /**
     * إدخال جميع قسائم الرواتب لمسير معين دفعة واحدة (Bulk Insert) لزيادة الأداء.
     *
     * @param int $payrollRunId
     * @param array $payslips
     * @return void
     */
    public function bulkInsert(int $payrollRunId, array $payslips): void;
}