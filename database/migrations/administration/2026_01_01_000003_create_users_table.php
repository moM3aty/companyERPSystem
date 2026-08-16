<?php
// Path: database/migrations/administration/2026_01_01_000003_create_users_table.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                username VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                employee_id BIGINT UNSIGNED NULL,
                language VARCHAR(10) DEFAULT 'en',
                timezone VARCHAR(100) DEFAULT 'UTC',
                is_active TINYINT(1) DEFAULT 1,
                failed_login_attempts INT DEFAULT 0,
                locked_until TIMESTAMP NULL DEFAULT NULL,
                last_login_at TIMESTAMP NULL DEFAULT NULL,
                password_changed_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_company_email (company_id, email),
                CONSTRAINT fk_users_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->connection->statement($sql);
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS users;");
    }
}