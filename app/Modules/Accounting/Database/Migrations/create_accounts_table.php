<?php
// Path: app/Modules/Accounting/Database/Migrations/create_accounts_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateAccountsTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `company_id` BIGINT UNSIGNED NOT NULL,
              `parent_id` BIGINT UNSIGNED DEFAULT NULL,
              `account_code` VARCHAR(50) NOT NULL,
              `account_name` VARCHAR(150) NOT NULL,
              `account_type` ENUM('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
              `normal_balance` ENUM('Debit','Credit') NOT NULL,
              `level` INT DEFAULT 1,
              `is_control_account` TINYINT(1) DEFAULT 0,
              `is_active` TINYINT(1) DEFAULT 1,
              `deleted_at` TIMESTAMP NULL DEFAULT NULL,
              UNIQUE KEY `unique_comp_acc_code` (`company_id`, `account_code`),
              CONSTRAINT `fk_coa_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
              CONSTRAINT `fk_coa_parent_v2` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `chart_of_accounts`;";
    }
}