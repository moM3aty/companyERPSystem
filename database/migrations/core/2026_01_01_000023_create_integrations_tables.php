<?php
// Path: database/migrations/core/2026_01_01_000023_create_integrations_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateIntegrationsTables extends Migration
{
    public function up(): void
    {
        // 1. API Keys (For external access to the ERP)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS api_keys (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                key_hash VARCHAR(255) NOT NULL,
                scopes JSON NULL,
                allowed_ips JSON NULL,
                expires_at TIMESTAMP NULL DEFAULT NULL,
                last_used_at TIMESTAMP NULL DEFAULT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_by BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_key_hash (key_hash),
                CONSTRAINT fk_apikey_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Webhooks (Pushing events from ERP to external systems)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS webhooks (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                event_name VARCHAR(100) NOT NULL,
                target_url VARCHAR(1000) NOT NULL,
                secret_key VARCHAR(255) NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_webhook_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Integration Configurations (e.g. Zatca, Stripe, Shopify settings)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS integrations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                provider VARCHAR(100) NOT NULL,
                base_url VARCHAR(1000) NULL,
                credentials JSON NULL, -- Encrypted Client IDs and Secrets
                sync_frequency INT DEFAULT 0,
                last_sync_at TIMESTAMP NULL DEFAULT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_integration_provider (company_id, provider),
                CONSTRAINT fk_int_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Integration Logs (For debugging 3rd party API calls)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS integration_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NULL,
                provider VARCHAR(100) NOT NULL,
                endpoint VARCHAR(1000) NOT NULL,
                method VARCHAR(10) NOT NULL,
                request_payload JSON NULL,
                response_payload JSON NULL,
                status_code INT NOT NULL,
                execution_time_ms DECIMAL(10, 2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS integration_logs;");
        $this->connection->statement("DROP TABLE IF EXISTS integrations;");
        $this->connection->statement("DROP TABLE IF EXISTS webhooks;");
        $this->connection->statement("DROP TABLE IF EXISTS api_keys;");
    }
}