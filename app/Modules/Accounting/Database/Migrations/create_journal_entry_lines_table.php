<?php
// Path: app/Modules/Accounting/Database/Migrations/create_journal_entry_lines_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateJournalEntryLinesTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `journal_entry_lines` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `journal_entry_id` BIGINT UNSIGNED NOT NULL,
              `account_id` BIGINT UNSIGNED NOT NULL,
              `cost_center_id` BIGINT UNSIGNED DEFAULT NULL,
              `department_id` BIGINT UNSIGNED DEFAULT NULL,
              `project_id` BIGINT UNSIGNED DEFAULT NULL,
              `debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
              `credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
              `description` TEXT DEFAULT NULL,
              CONSTRAINT `fk_jel_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
              CONSTRAINT `fk_jel_acc` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `journal_entry_lines`;";
    }
}