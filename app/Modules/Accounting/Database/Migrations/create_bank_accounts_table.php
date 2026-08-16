<?php
// Path: app/Modules/Accounting/Database/Migrations/create_bank_accounts_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateBankAccountsTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `bank_accounts` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `company_id` BIGINT UNSIGNED NOT NULL,
              `gl_account_id` BIGINT UNSIGNED NOT NULL,
              `account_name` VARCHAR(150) NOT NULL,
              `account_type` ENUM('bank', 'cash') DEFAULT 'bank',
              `currency_code` VARCHAR(10) DEFAULT 'SAR',
              `account_number` VARCHAR(100) DEFAULT NULL,
              `iban` VARCHAR(100) DEFAULT NULL,
              `current_balance` DECIMAL(15,2) DEFAULT 0.00,
              `is_active` TINYINT(1) DEFAULT 1,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              CONSTRAINT `fk_bank_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
              CONSTRAINT `fk_bank_gl` FOREIGN KEY (`gl_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `bank_accounts`;";
    }
}