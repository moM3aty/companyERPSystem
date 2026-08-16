<?php
// Path: app/Modules/Projects/Services/ProjectBillingService.php

declare(strict_types=1);

namespace App\Modules\Projects\Services;

use App\Core\Database\DatabaseManager;
use App\Core\Database\TransactionManager;
use App\Core\Exceptions\BusinessException;

class ProjectBillingService
{
    protected DatabaseManager $db;
    protected TransactionManager $transaction;

    public function __construct(DatabaseManager $db, TransactionManager $transaction)
    {
        $this->db = $db;
        $this->transaction = $transaction;
    }

    public function generateInvoiceFromTimesheets(int $projectId, float $hourlyRate, int $companyId): int
    {
        return $this->transaction->execute(function () use ($projectId, $hourlyRate, $companyId) {
            $unbilledHours = $this->db->connection()->selectOne(
                "SELECT SUM(hours) as total_hours FROM project_timesheets 
                 WHERE project_id = ? AND company_id = ? AND status = 'approved' AND is_billed = 0",
                [$projectId, $companyId]
            );

            $hours = (float)($unbilledHours['total_hours'] ?? 0);

            if ($hours <= 0) {
                throw new BusinessException("No unbilled approved timesheets found for this project.");
            }

            $totalAmount = $hours * $hourlyRate;

            $this->db->connection()->insert(
                "INSERT INTO project_invoices (company_id, project_id, invoice_amount, billing_type, status, created_at) 
                 VALUES (?, ?, ?, 'timesheet', 'draft', ?)",
                [$companyId, $projectId, $totalAmount, date('Y-m-d H:i:s')]
            );
            $projectInvoiceId = (int) $this->db->connection()->lastInsertId();

            $this->db->connection()->update(
                "UPDATE project_timesheets SET is_billed = 1, project_invoice_id = ? 
                 WHERE project_id = ? AND status = 'approved' AND is_billed = 0",
                [$projectInvoiceId, $projectId]
            );

            return $projectInvoiceId;
        });
    }
}