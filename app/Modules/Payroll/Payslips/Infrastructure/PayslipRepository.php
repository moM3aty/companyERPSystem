<?php
// Path: app/Modules/Payroll/Payslips/Infrastructure/PayslipRepository.php

declare(strict_types=1);

namespace App\Modules\Payroll\Payslips\Infrastructure;

use App\Core\Database\BaseRepository;
use App\Core\Database\DatabaseManager;
use App\Modules\Payroll\Payslips\Domain\PayslipRepositoryInterface;

/**
 * Enterprise Infrastructure Repository: Payslip
 */
class PayslipRepository extends BaseRepository implements PayslipRepositoryInterface
{
    protected string $table = 'payroll_payslips';
    protected bool $useTenantScope = false; // Linked via payroll_run_id

    public function __construct(DatabaseManager $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    public function bulkInsert(int $payrollRunId, array $payslips): void
    {
        if (empty($payslips)) {
            return;
        }

        $values = [];
        $bindings = [];
        $placeholders = "(?, ?, ?, ?, ?, ?, ?, ?)";

        foreach ($payslips as $payslip) {
            $values[] = $placeholders;
            array_push(
                $bindings,
                $payrollRunId,
                $payslip['employee_id'],
                $payslip['contract_id'],
                $payslip['basic_salary'],
                $payslip['allowances'],
                $payslip['deductions'],
                $payslip['net_salary'],
                json_encode($payslip['details'] ?? [], JSON_UNESCAPED_UNICODE)
            );
        }

        $sql = "INSERT INTO {$this->table} 
                (payroll_run_id, employee_id, contract_id, basic_salary, allowances, deductions, net_salary, details) 
                VALUES " . implode(', ', $values);

        $this->db->connection()->insert($sql, $bindings);
    }
}