<?php
// Path: database/migrations/pos/2026_01_01_000021_create_pos_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreatePosTables extends Migration
{
    public function up(): void
    {
        // 1. POS Terminals
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS pos_terminals (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(100) NOT NULL,
                code VARCHAR(50) NOT NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_pos_code (company_id, code),
                CONSTRAINT fk_pos_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. POS Shifts (Z-Reports)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS pos_shifts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                terminal_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                opened_at TIMESTAMP NOT NULL,
                closed_at TIMESTAMP NULL DEFAULT NULL,
                opening_amount DECIMAL(18, 4) DEFAULT 0.0000,
                closing_amount DECIMAL(18, 4) DEFAULT 0.0000,
                expected_amount DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('open', 'closed') DEFAULT 'open',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_shift_terminal FOREIGN KEY (terminal_id) REFERENCES pos_terminals(id) ON DELETE CASCADE,
                CONSTRAINT fk_shift_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. POS Orders
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS pos_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                shift_id BIGINT UNSIGNED NOT NULL,
                customer_id BIGINT UNSIGNED NULL,
                order_number VARCHAR(100) NOT NULL,
                subtotal DECIMAL(18, 4) DEFAULT 0.0000,
                tax_total DECIMAL(18, 4) DEFAULT 0.0000,
                discount_total DECIMAL(18, 4) DEFAULT 0.0000,
                grand_total DECIMAL(18, 4) NOT NULL,
                payment_method ENUM('cash', 'card') NOT NULL,
                status ENUM('completed', 'refunded') DEFAULT 'completed',
                created_by BIGINT UNSIGNED NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_pos_order_no (company_id, order_number),
                CONSTRAINT fk_porder_shift FOREIGN KEY (shift_id) REFERENCES pos_shifts(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. POS Order Items
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS pos_order_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                unit_price DECIMAL(18, 4) NOT NULL,
                tax_amount DECIMAL(18, 4) DEFAULT 0.0000,
                discount_amount DECIMAL(18, 4) DEFAULT 0.0000,
                total DECIMAL(18, 4) NOT NULL,
                CONSTRAINT fk_po_items_order FOREIGN KEY (order_id) REFERENCES pos_orders(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS pos_order_items;");
        $this->connection->statement("DROP TABLE IF EXISTS pos_orders;");
        $this->connection->statement("DROP TABLE IF EXISTS pos_shifts;");
        $this->connection->statement("DROP TABLE IF EXISTS pos_terminals;");
    }
}