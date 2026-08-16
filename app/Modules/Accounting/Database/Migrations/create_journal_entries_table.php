<?php
// Path: app/Modules/Accounting/Database/Migrations/create_journal_entries_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateJournalEntriesTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `journal_entries` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `company_id` BIGINT UNSIGNED NOT NULL,
              `branch_id` BIGINT UNSIGNED DEFAULT NULL,
              `entry_no` VARCHAR(50) NOT NULL,
              `entry_date` DATE NOT NULL,
              `reference_type` VARCHAR(50) DEFAULT NULL,
              `reference_id` BIGINT UNSIGNED DEFAULT NULL,
              `description` TEXT NOT NULL,
              `currency_id` BIGINT UNSIGNED DEFAULT NULL,
              `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
              `status` ENUM('draft', 'posted', 'voided') DEFAULT 'draft',
              `posted_by` BIGINT UNSIGNED DEFAULT NULL,
              `posted_at` DATETIME DEFAULT NULL,
              `created_by` BIGINT UNSIGNED DEFAULT NULL,
              `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              UNIQUE KEY `unique_comp_je` (`company_id`, `entry_no`),
              CONSTRAINT `fk_je_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `journal_entries`;";
    }
}