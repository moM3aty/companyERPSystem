<?php
// Path: database/migrations/accounting/2026_01_01_000010_create_accounting_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateAccountingTables extends Migration
{
    public function up(): void
    {
        // 1. Fiscal Periods
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS fiscal_periods (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                year INT NOT NULL,
                month INT NOT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                is_closed TINYINT(1) DEFAULT 0,
                closed_at TIMESTAMP NULL DEFAULT NULL,
                closed_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_period (company_id, year, month),
                CONSTRAINT fk_fp_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Chart of Accounts
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS chart_of_accounts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED NULL,
                account_code VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
                is_control_account TINYINT(1) DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                level INT DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_account_code (company_id, account_code),
                CONSTRAINT fk_coa_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_coa_parent FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Journal Entries (Header)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS journal_entries (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                entry_no VARCHAR(100) NOT NULL,
                entry_date DATE NOT NULL,
                reference_type VARCHAR(100) NULL,
                reference_id BIGINT UNSIGNED NULL,
                description TEXT NOT NULL,
                currency_id BIGINT UNSIGNED NULL,
                exchange_rate DECIMAL(15, 6) DEFAULT 1.000000,
                status ENUM('draft', 'posted', 'voided') DEFAULT 'draft',
                posted_by BIGINT UNSIGNED NULL,
                posted_at TIMESTAMP NULL DEFAULT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_entry_no (company_id, entry_no),
                CONSTRAINT fk_je_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Journal Entry Lines
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS journal_entry_lines (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                journal_entry_id BIGINT UNSIGNED NOT NULL,
                account_id BIGINT UNSIGNED NOT NULL,
                cost_center_id BIGINT UNSIGNED NULL,
                department_id BIGINT UNSIGNED NULL,
                project_id BIGINT UNSIGNED NULL,
                debit DECIMAL(18, 4) DEFAULT 0.0000,
                credit DECIMAL(18, 4) DEFAULT 0.0000,
                description VARCHAR(255) NULL,
                CONSTRAINT fk_jel_entry FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
                CONSTRAINT fk_jel_account FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Add index to speed up General Ledger reporting
        $this->connection->statement("CREATE INDEX idx_jel_account ON journal_entry_lines(account_id);");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS journal_entry_lines;");
        $this->connection->statement("DROP TABLE IF EXISTS journal_entries;");
        $this->connection->statement("DROP TABLE IF EXISTS chart_of_accounts;");
        $this->connection->statement("DROP TABLE IF EXISTS fiscal_periods;");
    }
}