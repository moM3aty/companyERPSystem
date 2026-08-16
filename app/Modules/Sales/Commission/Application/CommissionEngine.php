<?php
// Path: app/Modules/Sales/Commission/Application/CommissionEngine.php

declare(strict_types=1);

namespace App\Modules\Sales\Commission\Application;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Modules\Sales\Commission\Domain\CommissionPlanRepositoryInterface;
use App\Core\Exceptions\BusinessException;

/**
 * Enterprise Application Service: Commission Engine
 * محرك احتساب العمولات. يقوم بفحص فواتير المبيعات المدفوعة لمندوب معين واستخراج العمولات المستحقة.
 */
class CommissionEngine
{
    protected CommissionPlanRepositoryInterface $planRepo;
    protected TransactionManager $transaction;
    protected DatabaseManager $db;

    public function __construct(
        CommissionPlanRepositoryInterface $planRepo,
        TransactionManager $transaction,
        DatabaseManager $db
    ) {
        $this->planRepo = $planRepo;
        $this->transaction = $transaction;
        $this->db = $db;
    }

    /**
     * احتساب وإصدار العمولات لمندوب مبيعات عن فترة محددة.
     *
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @param int $companyId
     * @return int عدد العمولات التي تم احتسابها
     * @throws BusinessException|\Throwable
     */
    public function calculateCommissions(int $employeeId, string $startDate, string $endDate, int $companyId): int
    {
        return $this->transaction->execute(function () use ($employeeId, $startDate, $endDate, $companyId) {
            
            $plan = $this->planRepo->getActivePlanForEmployee($employeeId, $companyId);

            if (!$plan) {
                throw new BusinessException("No active commission plan found for this employee.", 404);
            }

            // جلب الفواتير المدفوعة بالكامل والتي لم يتم احتساب عمولتها بعد
            $sql = "SELECT id, grand_total FROM sales_invoices 
                    WHERE created_by = ? AND company_id = ? 
                      AND invoice_date BETWEEN ? AND ? 
                      AND status = 'paid'
                      AND id NOT IN (SELECT sales_invoice_id FROM sales_commission_records WHERE employee_id = ?)";
                      
            $invoices = $this->db->connection()->select($sql, [$employeeId, $companyId, $startDate, $endDate, $employeeId]);

            $calculatedCount = 0;
            $now = date('Y-m-d H:i:s');

            foreach ($invoices as $invoice) {
                $invoiceAmount = (float) $invoice['grand_total'];
                $commissionAmount = 0.0;

                if ($plan['type'] === 'percentage') {
                    $commissionAmount = $invoiceAmount * ((float) $plan['value'] / 100);
                } else {
                    $commissionAmount = (float) $plan['value'];
                }

                if ($commissionAmount > 0) {
                    $this->db->connection()->insert(
                        "INSERT INTO sales_commission_records (company_id, employee_id, commission_plan_id, sales_invoice_id, invoice_amount, commission_amount, status, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)",
                        [$companyId, $employeeId, $plan['id'], $invoice['id'], $invoiceAmount, round($commissionAmount, 2), $now]
                    );
                    $calculatedCount++;
                }
            }

            return $calculatedCount;
        });
    }
}