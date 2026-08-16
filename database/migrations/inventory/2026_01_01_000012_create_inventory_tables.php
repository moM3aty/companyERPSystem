<?php
// Path: database/migrations/inventory/2026_01_01_000012_create_inventory_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateInventoryTables extends Migration
{
    public function up(): void
    {
        // 1. Warehouses
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS warehouses (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NOT NULL,
                location_id BIGINT UNSIGNED NULL,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(150) NOT NULL,
                address VARCHAR(255) NULL,
                is_active TINYINT(1) DEFAULT 1,
                is_transit TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_wh_code (company_id, code),
                CONSTRAINT fk_wh_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Product Categories
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS product_categories (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED NULL,
                code VARCHAR(50) NULL,
                name VARCHAR(150) NOT NULL,
                description TEXT NULL,
                level INT DEFAULT 1,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_cat_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_cat_parent FOREIGN KEY (parent_id) REFERENCES product_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Products
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS products (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                category_id BIGINT UNSIGNED NULL,
                brand_id BIGINT UNSIGNED NULL,
                type ENUM('storable', 'service', 'consumable') NOT NULL DEFAULT 'storable',
                cost_method ENUM('fifo', 'average', 'standard') NOT NULL DEFAULT 'average',
                code VARCHAR(100) NOT NULL,
                barcode VARCHAR(100) NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                base_unit_id BIGINT UNSIGNED NULL,
                default_tax_id BIGINT UNSIGNED NULL,
                default_price DECIMAL(18, 4) DEFAULT 0.0000,
                is_active TINYINT(1) DEFAULT 1,
                track_batches TINYINT(1) DEFAULT 0,
                track_serials TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_prod_code (company_id, code),
                UNIQUE KEY unique_prod_barcode (company_id, barcode),
                CONSTRAINT fk_prod_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_prod_category FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Inventory Stocks (الرصيد اللحظي)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS inventory_stocks (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                warehouse_id BIGINT UNSIGNED NOT NULL,
                quantity DECIMAL(18, 4) DEFAULT 0.0000,
                reserved_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                average_cost DECIMAL(18, 4) DEFAULT 0.0000,
                last_movement_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_stock (company_id, product_id, warehouse_id),
                CONSTRAINT fk_stk_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                CONSTRAINT fk_stk_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 5. Stock Movements (Item Ledger / كارتة الصنف)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS inventory_stock_movements (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                warehouse_id BIGINT UNSIGNED NOT NULL,
                movement_type ENUM('IN', 'OUT', 'TRANSFER', 'ADJUSTMENT') NOT NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                balance_after DECIMAL(18, 4) NOT NULL,
                unit_cost DECIMAL(18, 4) DEFAULT 0.0000,
                reference_type VARCHAR(100) NOT NULL,
                reference_id BIGINT UNSIGNED NOT NULL,
                notes VARCHAR(255) NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_mov_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                CONSTRAINT fk_mov_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        $this->connection->statement("CREATE INDEX idx_mov_reference ON inventory_stock_movements(reference_type, reference_id);");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS inventory_stock_movements;");
        $this->connection->statement("DROP TABLE IF EXISTS inventory_stocks;");
        $this->connection->statement("DROP TABLE IF EXISTS products;");
        $this->connection->statement("DROP TABLE IF EXISTS product_categories;");
        $this->connection->statement("DROP TABLE IF EXISTS warehouses;");
    }
}