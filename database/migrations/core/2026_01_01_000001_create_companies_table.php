<?php
// Path: database/migrations/core/2026_01_01_000001_create_companies_table.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateCompaniesTable extends Migration
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS companies (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                registration_number VARCHAR(100) NOT NULL UNIQUE,
                tax_number VARCHAR(100) NULL,
                base_currency_id BIGINT UNSIGNED NULL,
                timezone VARCHAR(100) DEFAULT 'UTC',
                status ENUM('active', 'suspended') DEFAULT 'active',
                enforce_ip_whitelist TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                INDEX idx_companies_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->connection->statement($sql);
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS companies;");
    }
}