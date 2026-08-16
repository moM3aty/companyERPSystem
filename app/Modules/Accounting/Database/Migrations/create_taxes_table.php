<?php
// Path: app/Modules/Accounting/Database/Migrations/create_taxes_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateTaxesTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `taxes` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `company_id` BIGINT UNSIGNED NOT NULL,
              `code` VARCHAR(50) NOT NULL,
              `name` VARCHAR(100) NOT NULL,
              `tax_type` ENUM('VAT', 'Withholding', 'Custom', 'Exempt', 'Zero') DEFAULT 'VAT',
              `rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
              `sales_account_id` BIGINT UNSIGNED DEFAULT NULL,
              `purchase_account_id` BIGINT UNSIGNED DEFAULT NULL,
              `is_active` TINYINT(1) DEFAULT 1,
              CONSTRAINT `fk_tax_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
              CONSTRAINT `fk_tax_sales_acc` FOREIGN KEY (`sales_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `taxes`;";
    }
}