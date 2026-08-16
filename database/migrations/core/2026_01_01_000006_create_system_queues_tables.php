<?php
// Path: database/migrations/core/2026_01_01_000006_create_system_queues_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateSystemQueuesTables extends Migration
{
    public function up(): void
    {
        // 1. Background Jobs Queue
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(100) NOT NULL INDEX,
                payload LONGTEXT NOT NULL,
                attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
                reserved_at INT UNSIGNED NULL,
                available_at INT UNSIGNED NOT NULL,
                created_at INT UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Dead Letter Queue (Failed Jobs)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS failed_jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(100) NOT NULL,
                payload LONGTEXT NOT NULL,
                exception LONGTEXT NOT NULL,
                failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Outbox Pattern Messages (Event Sourcing)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS outbox_messages (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                event_name VARCHAR(255) NOT NULL,
                payload JSON NOT NULL,
                is_processed TINYINT(1) DEFAULT 0,
                processed_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_outbox_processing (is_processed, id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS outbox_messages;");
        $this->connection->statement("DROP TABLE IF EXISTS failed_jobs;");
        $this->connection->statement("DROP TABLE IF EXISTS jobs;");
    }
}