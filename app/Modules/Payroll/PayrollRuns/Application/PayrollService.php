<?php
// Path: app/Modules/Payroll/PayrollRuns/Application/PayrollService.php

declare(strict_types=1);

namespace App\Modules\Payroll\PayrollRuns\Application;

use App\Modules\Payroll\PayrollRuns\Domain\PayrollRunRepositoryInterface;
use App\Modules\Payroll\Payslips\Domain\PayslipRepositoryInterface;
use App\Modules\Payroll\PayrollRuns\Domain\Events\PayrollRunProcessedEvent;
use App\Core\Database\TransactionManager;
use App\Core\Database\DatabaseManager;
use App\Core\Events\EventBus;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Payroll Engine
 * محرك الرواتب: يجمع بيانات العقود، يحسب البدلات/الخصومات، ويولد المسير والقسائم في خطوة واحدة ذرية.
 */
class PayrollService
{
    protected PayrollRunRepositoryInterface $runRepo;
    protected PayslipRepositoryInterface $payslipRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;
    protected EventBus $eventBus;

    public function __construct(
        PayrollRunRepositoryInterface $runRepo,
        PayslipRepositoryInterface $payslipRepo,
        TransactionManager $transaction,
        DatabaseManager $db,
        EventBus $eventBus
    ) {
        $this->runRepo = $runRepo;
        $this->payslipRepo = $payslipRepo;
        $this->transaction = $transaction;
        $this->db = $db;
        $this->eventBus = $eventBus;
    }

    /**
     * تشغيل محرك احتساب الرواتب لشهر معين.
     *
     * @param string $runPeriod (YYYY-MM)
     * @param int $companyId
     * @param int $userId
     * @return array
     * @throws BusinessException|\Throwable
     */
    public function processPayroll(string $runPeriod, int $companyId, int $userId): array
    {
        if ($this->runRepo->existsForPeriod($runPeriod, $companyId)) {
            throw new BusinessException("A payroll run for the period [{$runPeriod}] has already been processed.");
        }

        return $this->transaction->execute(function () use ($runPeriod, $companyId, $userId) {
            
            // 1. Fetch Active Contracts for the Company
            // Note: We use raw DB query for extreme performance rather than loading thousands of ORM objects
            $contracts = $this->db->connection()->select(
                "SELECT id, employee_id, basic_salary FROM hr_contracts WHERE company_id = ? AND status = 'active'",
                [$companyId]
            );

            if (empty($contracts)) {
                throw new BusinessException("No active employee contracts found to process payroll.");
            }

            $totalBasic = 0.0;
            $totalAllowances = 0.0;
            $totalDeductions = 0.0;
            $netTotal = 0.0;
            $payslips = [];

            // 2. Compute Salary per Employee
            foreach ($contracts as $contract) {
                $basic = (float) $contract['basic_salary'];
                
                // Here you would integrate with Attendance/Overtime modules
                // For foundation: we assume 0 additional allowances/deductions
                $allowances = 0.0; 
                $deductions = 0.0;
                
                $netSalary = $basic + $allowances - $deductions;

                $totalBasic += $basic;
                $totalAllowances += $allowances;
                $totalDeductions += $deductions;
                $netTotal += $netSalary;

                $payslips[] = [
                    'employee_id'  => $contract['employee_id'],
                    'contract_id'  => $contract['id'],
                    'basic_salary' => $basic,
                    'allowances'   => $allowances,
                    'deductions'   => $deductions,
                    'net_salary'   => $netSalary,
                    'details'      => ['basic' => $basic, 'allowances' => [], 'deductions' => []]
                ];
            }

            // 3. Create Payroll Run Header
            $runId = $this->runRepo->create([
                'company_id'       => $companyId,
                'run_reference'    => "PR-{$runPeriod}",
                'run_period'       => $runPeriod,
                'total_basic'      => $totalBasic,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'net_total'        => $netTotal,
                'status'           => 'draft', // Requires final approval to be posted
                'created_by'       => $userId,
                'created_at'       => date('Y-m-d H:i:s')
            ]);

            // 4. Bulk Insert Payslips
            $this->payslipRepo->bulkInsert($runId, $payslips);

            // 5. Publish Event (Audit & Accounting listeners will react)
            $this->eventBus->publish(new PayrollRunProcessedEvent($runId, $companyId, $runPeriod, $netTotal));

            $this->runRepo->setTenantId($companyId);
            return $this->runRepo->findOrFail($runId);
        });
    }
}