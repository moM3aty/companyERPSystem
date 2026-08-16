<?php
// Path: database/migrations/manufacturing/2026_01_01_000019_create_manufacturing_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateManufacturingTables extends Migration
{
    public function up(): void
    {
        // 1. Bill of Materials - Header
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS manufacturing_boms (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL, -- Finished Good
                code VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                batch_quantity DECIMAL(18, 4) NOT NULL DEFAULT 1.0000,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_bom_code (company_id, code),
                CONSTRAINT fk_bom_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_bom_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Bill of Materials - Items
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS manufacturing_bom_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bom_id BIGINT UNSIGNED NOT NULL,
                component_product_id BIGINT UNSIGNED NOT NULL, -- Raw Material
                quantity DECIMAL(18, 4) NOT NULL,
                unit_id BIGINT UNSIGNED NULL,
                scrap_percentage DECIMAL(5, 2) DEFAULT 0.00,
                CONSTRAINT fk_bomi_bom FOREIGN KEY (bom_id) REFERENCES manufacturing_boms(id) ON DELETE CASCADE,
                CONSTRAINT fk_bomi_component FOREIGN KEY (component_product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Production Orders - Header
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS manufacturing_production_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                bom_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                order_number VARCHAR(100) NOT NULL,
                planned_quantity DECIMAL(18, 4) NOT NULL,
                produced_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'planned',
                start_date DATE NOT NULL,
                end_date DATE NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_prod_order_no (company_id, order_number),
                CONSTRAINT fk_po_bom FOREIGN KEY (bom_id) REFERENCES manufacturing_boms(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Production Orders - Items (Raw Material Consumption)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS manufacturing_production_order_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                production_order_id BIGINT UNSIGNED NOT NULL,
                component_product_id BIGINT UNSIGNED NOT NULL,
                required_quantity DECIMAL(18, 4) NOT NULL,
                consumed_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                CONSTRAINT fk_poi_po FOREIGN KEY (production_order_id) REFERENCES manufacturing_production_orders(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS manufacturing_production_order_items;");
        $this->connection->statement("DROP TABLE IF EXISTS manufacturing_production_orders;");
        $this->connection->statement("DROP TABLE IF EXISTS manufacturing_bom_items;");
        $this->connection->statement("DROP TABLE IF EXISTS manufacturing_boms;");
    }
}