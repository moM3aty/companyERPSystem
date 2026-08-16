<?php
// Path: database/migrations/treasury/2026_01_01_000011_create_treasury_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateTreasuryTables extends Migration
{
    public function up(): void
    {
        // 1. Treasury Accounts (Cash / Banks)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS treasury_accounts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(150) NOT NULL,
                type ENUM('cash', 'bank') NOT NULL,
                account_number VARCHAR(100) NULL,
                currency_id BIGINT UNSIGNED NULL,
                gl_account_id BIGINT UNSIGNED NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_ta_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_ta_gl FOREIGN KEY (gl_account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Receipts (سندات القبض)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS treasury_receipts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                receipt_no VARCHAR(100) NOT NULL,
                receipt_date DATE NOT NULL,
                treasury_account_id BIGINT UNSIGNED NOT NULL,
                credit_account_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(18, 4) NOT NULL,
                currency_id BIGINT UNSIGNED NULL,
                exchange_rate DECIMAL(15, 6) DEFAULT 1.000000,
                reference VARCHAR(150) NULL,
                description TEXT NOT NULL,
                journal_entry_id BIGINT UNSIGNED NULL,
                status ENUM('draft', 'posted', 'voided') DEFAULT 'posted',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_receipt_no (company_id, receipt_no),
                CONSTRAINT fk_tr_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_tr_account FOREIGN KEY (treasury_account_id) REFERENCES treasury_accounts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Payment Vouchers (سندات الصرف)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS treasury_payment_vouchers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                voucher_no VARCHAR(100) NOT NULL,
                voucher_date DATE NOT NULL,
                treasury_account_id BIGINT UNSIGNED NOT NULL,
                debit_account_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(18, 4) NOT NULL,
                currency_id BIGINT UNSIGNED NULL,
                exchange_rate DECIMAL(15, 6) DEFAULT 1.000000,
                reference VARCHAR(150) NULL,
                description TEXT NOT NULL,
                journal_entry_id BIGINT UNSIGNED NULL,
                status ENUM('draft', 'posted', 'voided') DEFAULT 'posted',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_voucher_no (company_id, voucher_no),
                CONSTRAINT fk_tpv_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_tpv_account FOREIGN KEY (treasury_account_id) REFERENCES treasury_accounts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS treasury_payment_vouchers;");
        $this->connection->statement("DROP TABLE IF EXISTS treasury_receipts;");
        $this->connection->statement("DROP TABLE IF EXISTS treasury_accounts;");
    }
}