<?php
// Path: database/migrations/assets/2026_01_01_000017_create_assets_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateAssetsTables extends Migration
{
    public function up(): void
    {
        // 1. Fixed Assets (الأصول الثابتة)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS fixed_assets (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                asset_category_id BIGINT UNSIGNED NULL,
                asset_code VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                purchase_date DATE NOT NULL,
                purchase_value DECIMAL(18, 4) NOT NULL,
                salvage_value DECIMAL(18, 4) DEFAULT 0.0000,
                useful_life_months INT NOT NULL,
                accumulated_depreciation DECIMAL(18, 4) DEFAULT 0.0000,
                net_book_value DECIMAL(18, 4) NOT NULL,
                asset_account_id BIGINT UNSIGNED NOT NULL,
                accumulated_depreciation_account_id BIGINT UNSIGNED NOT NULL,
                depreciation_expense_account_id BIGINT UNSIGNED NOT NULL,
                status ENUM('active', 'disposed', 'sold', 'under_maintenance') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_asset_code (company_id, asset_code),
                CONSTRAINT fk_asset_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_asset_account FOREIGN KEY (asset_account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Asset Depreciations (سجلات الإهلاك)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS asset_depreciations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                asset_id BIGINT UNSIGNED NOT NULL,
                period_year INT NOT NULL,
                period_month INT NOT NULL,
                depreciation_amount DECIMAL(18, 4) NOT NULL,
                journal_entry_id BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_asset_depreciation (asset_id, period_year, period_month),
                CONSTRAINT fk_depreciation_asset FOREIGN KEY (asset_id) REFERENCES fixed_assets(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS asset_depreciations;");
        $this->connection->statement("DROP TABLE IF EXISTS fixed_assets;");
    }
}