<?php
// Path: app/Modules/Accounting/Database/Migrations/create_cost_centers_table.php

declare(strict_types=1);

namespace App\Modules\Accounting\Database\Migrations;

class CreateCostCentersTable
{
    public function up(): string
    {
        return "
            CREATE TABLE IF NOT EXISTS `cost_centers` (
              `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
              `company_id` BIGINT UNSIGNED NOT NULL,
              `parent_id` BIGINT UNSIGNED DEFAULT NULL,
              `code` VARCHAR(50) NOT NULL,
              `name` VARCHAR(150) NOT NULL,
              `department_id` BIGINT UNSIGNED DEFAULT NULL,
              `is_active` TINYINT(1) DEFAULT 1,
              CONSTRAINT `fk_cc_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
    }

    public function down(): string
    {
        return "DROP TABLE IF EXISTS `cost_centers`;";
    }
}