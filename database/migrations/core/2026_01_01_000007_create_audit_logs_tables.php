<?php
// Path: database/migrations/core/2026_01_01_000007_create_audit_logs_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateAuditLogsTables extends Migration
{
    public function up(): void
    {
        // 1. Data Mutation Logs (Entity Auditing)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                user_id BIGINT UNSIGNED NULL,
                action VARCHAR(50) NOT NULL,
                entity_type VARCHAR(100) NOT NULL,
                entity_id BIGINT UNSIGNED NOT NULL,
                old_values JSON NULL,
                new_values JSON NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_audit_entity (entity_type, entity_id),
                INDEX idx_audit_company (company_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. User Activity Logs
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                user_id BIGINT UNSIGNED NULL,
                activity_type VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                metadata JSON NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_activity_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS activity_logs;");
        $this->connection->statement("DROP TABLE IF EXISTS audit_logs;");
    }
}