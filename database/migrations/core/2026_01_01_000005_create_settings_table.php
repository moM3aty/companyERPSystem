<?php
// Path: database/migrations/core/2026_01_01_000005_create_settings_table.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS settings (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                scope ENUM('global', 'company', 'branch', 'user') NOT NULL,
                scope_id BIGINT UNSIGNED NULL,
                `key` VARCHAR(150) NOT NULL,
                `value` JSON NULL,
                `type` VARCHAR(50) DEFAULT 'string',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_setting (scope, scope_id, `key`),
                INDEX idx_settings_lookup (scope, scope_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $this->connection->statement($sql);
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS settings;");
    }
}