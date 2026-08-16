<?php
// Path: database/migrations/core/2026_01_01_000009_create_master_data_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateMasterDataTables extends Migration
{
    public function up(): void
    {
        // 1. Countries
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS countries (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(5) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                dial_code VARCHAR(10) NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Currencies
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS currencies (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(10) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                symbol VARCHAR(10) NOT NULL,
                exchange_rate DECIMAL(15, 6) DEFAULT 1.000000,
                is_base TINYINT(1) DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Exchange Rates
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS exchange_rates (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                base_currency_id BIGINT UNSIGNED NOT NULL,
                target_currency_id BIGINT UNSIGNED NOT NULL,
                rate DECIMAL(15, 6) NOT NULL,
                effective_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_er_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_er_base FOREIGN KEY (base_currency_id) REFERENCES currencies(id) ON DELETE CASCADE,
                CONSTRAINT fk_er_target FOREIGN KEY (target_currency_id) REFERENCES currencies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Units of Measurement
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS units (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(100) NOT NULL,
                type VARCHAR(50) DEFAULT 'piece',
                base_unit_id BIGINT UNSIGNED NULL,
                conversion_factor DECIMAL(15, 6) DEFAULT 1.000000,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_units_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_units_base FOREIGN KEY (base_unit_id) REFERENCES units(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 5. Taxes
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS taxes (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(100) NOT NULL,
                type ENUM('percentage', 'fixed') DEFAULT 'percentage',
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_taxes_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 6. Generic Lookups (Dropdowns)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS lookups (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                `type` VARCHAR(100) NOT NULL,
                code VARCHAR(100) NOT NULL,
                `value` VARCHAR(255) NOT NULL,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_lookups_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS lookups;");
        $this->connection->statement("DROP TABLE IF EXISTS taxes;");
        $this->connection->statement("DROP TABLE IF EXISTS units;");
        $this->connection->statement("DROP TABLE IF EXISTS exchange_rates;");
        $this->connection->statement("DROP TABLE IF EXISTS currencies;");
        $this->connection->statement("DROP TABLE IF EXISTS countries;");
    }
}