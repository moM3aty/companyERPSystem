<?php
// Path: database/migrations/payroll/2026_01_01_000016_create_payroll_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreatePayrollTables extends Migration
{
    public function up(): void
    {
        // 1. Payroll Runs (المسير)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS payroll_runs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                run_reference VARCHAR(50) NOT NULL,
                run_period VARCHAR(10) NOT NULL, -- Format YYYY-MM
                total_basic DECIMAL(18, 4) DEFAULT 0.0000,
                total_allowances DECIMAL(18, 4) DEFAULT 0.0000,
                total_deductions DECIMAL(18, 4) DEFAULT 0.0000,
                net_total DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'approved', 'posted', 'cancelled') DEFAULT 'draft',
                journal_entry_id BIGINT UNSIGNED NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_pr_period (company_id, run_period),
                CONSTRAINT fk_pr_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Payslips (قسائم الرواتب الفردية)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS payroll_payslips (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                payroll_run_id BIGINT UNSIGNED NOT NULL,
                employee_id BIGINT UNSIGNED NOT NULL,
                contract_id BIGINT UNSIGNED NOT NULL,
                basic_salary DECIMAL(18, 4) NOT NULL,
                allowances DECIMAL(18, 4) DEFAULT 0.0000,
                deductions DECIMAL(18, 4) DEFAULT 0.0000,
                net_salary DECIMAL(18, 4) NOT NULL,
                details JSON NULL, -- Historical breakdown
                CONSTRAINT fk_ps_run FOREIGN KEY (payroll_run_id) REFERENCES payroll_runs(id) ON DELETE CASCADE,
                CONSTRAINT fk_ps_employee FOREIGN KEY (employee_id) REFERENCES hr_employees(id) ON DELETE RESTRICT,
                CONSTRAINT fk_ps_contract FOREIGN KEY (contract_id) REFERENCES hr_contracts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS payroll_payslips;");
        $this->connection->statement("DROP TABLE IF EXISTS payroll_runs;");
    }
}