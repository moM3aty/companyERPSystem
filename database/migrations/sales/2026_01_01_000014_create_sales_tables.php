<?php
// Path: database/migrations/sales/2026_01_01_000014_create_sales_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreateSalesTables extends Migration
{
    public function up(): void
    {
        // 1. Customers
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS customers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                customer_code VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(150) NULL,
                phone VARCHAR(50) NULL,
                tax_number VARCHAR(100) NULL,
                credit_limit DECIMAL(18, 4) DEFAULT 0.0000,
                payment_term_id BIGINT UNSIGNED NULL,
                price_list_id BIGINT UNSIGNED NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_cust_code (company_id, customer_code),
                CONSTRAINT fk_cust_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS customer_contacts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id BIGINT UNSIGNED NOT NULL,
                name VARCHAR(150) NOT NULL,
                job_title VARCHAR(100) NULL,
                email VARCHAR(150) NULL,
                phone VARCHAR(50) NULL,
                mobile VARCHAR(50) NULL,
                is_primary TINYINT(1) DEFAULT 0,
                CONSTRAINT fk_cc_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Sales Orders
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS sales_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                order_no VARCHAR(50) NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                quotation_id BIGINT UNSIGNED NULL,
                order_date DATE NOT NULL,
                delivery_date DATE NULL,
                currency_id BIGINT UNSIGNED NULL,
                subtotal DECIMAL(18, 4) DEFAULT 0.0000,
                discount_total DECIMAL(18, 4) DEFAULT 0.0000,
                tax_total DECIMAL(18, 4) DEFAULT 0.0000,
                grand_total DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'confirmed', 'processing', 'shipped', 'invoiced', 'cancelled') DEFAULT 'draft',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_so_number (company_id, order_no),
                CONSTRAINT fk_so_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_so_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS sales_order_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                sales_order_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                description VARCHAR(255) NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                delivered_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                invoiced_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                unit_price DECIMAL(18, 4) NOT NULL,
                discount_amount DECIMAL(18, 4) DEFAULT 0.0000,
                tax_amount DECIMAL(18, 4) DEFAULT 0.0000,
                total DECIMAL(18, 4) NOT NULL,
                CONSTRAINT fk_soi_so FOREIGN KEY (sales_order_id) REFERENCES sales_orders(id) ON DELETE CASCADE,
                CONSTRAINT fk_soi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Sales Invoices
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS sales_invoices (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                invoice_no VARCHAR(50) NOT NULL,
                customer_id BIGINT UNSIGNED NOT NULL,
                invoice_date DATE NOT NULL,
                due_date DATE NOT NULL,
                currency_id BIGINT UNSIGNED NULL,
                subtotal DECIMAL(18, 4) DEFAULT 0.0000,
                discount_total DECIMAL(18, 4) DEFAULT 0.0000,
                tax_total DECIMAL(18, 4) DEFAULT 0.0000,
                grand_total DECIMAL(18, 4) DEFAULT 0.0000,
                paid_amount DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'approved', 'posted', 'paid', 'voided') DEFAULT 'draft',
                journal_entry_id BIGINT UNSIGNED NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_si_number (company_id, invoice_no),
                CONSTRAINT fk_si_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_si_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS sales_invoice_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                invoice_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                description VARCHAR(255) NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                unit_price DECIMAL(18, 4) NOT NULL,
                discount_amount DECIMAL(18, 4) DEFAULT 0.0000,
                tax_amount DECIMAL(18, 4) DEFAULT 0.0000,
                total DECIMAL(18, 4) NOT NULL,
                warehouse_id BIGINT UNSIGNED NULL,
                CONSTRAINT fk_sii_si FOREIGN KEY (invoice_id) REFERENCES sales_invoices(id) ON DELETE CASCADE,
                CONSTRAINT fk_sii_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Invoice Allocations (Customer Payments)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS sales_invoice_allocations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                receipt_id BIGINT UNSIGNED NOT NULL,
                sales_invoice_id BIGINT UNSIGNED NOT NULL,
                allocated_amount DECIMAL(18, 4) NOT NULL,
                allocated_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_alloc_receipt FOREIGN KEY (receipt_id) REFERENCES treasury_receipts(id) ON DELETE CASCADE,
                CONSTRAINT fk_alloc_invoice FOREIGN KEY (sales_invoice_id) REFERENCES sales_invoices(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS sales_invoice_allocations;");
        $this->connection->statement("DROP TABLE IF EXISTS sales_invoice_items;");
        $this->connection->statement("DROP TABLE IF EXISTS sales_invoices;");
        $this->connection->statement("DROP TABLE IF EXISTS sales_order_items;");
        $this->connection->statement("DROP TABLE IF EXISTS sales_orders;");
        $this->connection->statement("DROP TABLE IF EXISTS customer_contacts;");
        $this->connection->statement("DROP TABLE IF EXISTS customers;");
    }
}