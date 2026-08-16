<?php
// Path: database/migrations/purchasing/2026_01_01_000013_create_purchasing_tables.php

declare(strict_types=1);

use App\Core\Database\Migration;

class CreatePurchasingTables extends Migration
{
    public function up(): void
    {
        // 1. Suppliers
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS suppliers (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                supplier_code VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(150) NULL,
                phone VARCHAR(50) NULL,
                tax_number VARCHAR(100) NULL,
                credit_limit DECIMAL(18, 4) DEFAULT 0.0000,
                payment_term_id BIGINT UNSIGNED NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL,
                UNIQUE KEY unique_supp_code (company_id, supplier_code),
                CONSTRAINT fk_supp_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 2. Purchase Requisitions (Internal PR)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_requisitions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                pr_number VARCHAR(50) NOT NULL,
                requester_id BIGINT UNSIGNED NOT NULL,
                department_id BIGINT UNSIGNED NOT NULL,
                request_date DATE NOT NULL,
                required_date DATE NOT NULL,
                justification TEXT NULL,
                total_estimated DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'pending_approval', 'approved', 'rejected', 'ordered') DEFAULT 'draft',
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_pr_number (company_id, pr_number),
                CONSTRAINT fk_pr_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_requisition_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                purchase_requisition_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                description VARCHAR(255) NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                estimated_unit_price DECIMAL(18, 4) DEFAULT 0.0000,
                total_estimated DECIMAL(18, 4) DEFAULT 0.0000,
                CONSTRAINT fk_pri_pr FOREIGN KEY (purchase_requisition_id) REFERENCES purchase_requisitions(id) ON DELETE CASCADE,
                CONSTRAINT fk_pri_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 3. Purchase Orders (PO)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                po_number VARCHAR(50) NOT NULL,
                supplier_id BIGINT UNSIGNED NOT NULL,
                order_date DATE NOT NULL,
                expected_delivery_date DATE NULL,
                currency_id BIGINT UNSIGNED NULL,
                subtotal DECIMAL(18, 4) DEFAULT 0.0000,
                discount_total DECIMAL(18, 4) DEFAULT 0.0000,
                tax_total DECIMAL(18, 4) DEFAULT 0.0000,
                grand_total DECIMAL(18, 4) DEFAULT 0.0000,
                status ENUM('draft', 'approved', 'sent', 'received', 'cancelled') DEFAULT 'draft',
                notes TEXT NULL,
                created_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_po_number (company_id, po_number),
                CONSTRAINT fk_po_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_po_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_order_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                purchase_order_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                description VARCHAR(255) NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                unit_price DECIMAL(18, 4) NOT NULL,
                discount_amount DECIMAL(18, 4) DEFAULT 0.0000,
                tax_amount DECIMAL(18, 4) DEFAULT 0.0000,
                total DECIMAL(18, 4) NOT NULL,
                CONSTRAINT fk_poi_po FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
                CONSTRAINT fk_poi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 4. Goods Receipt Notes (GRN)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchasing_goods_receipts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                receipt_no VARCHAR(50) NOT NULL,
                purchase_order_id BIGINT UNSIGNED NULL,
                supplier_id BIGINT UNSIGNED NOT NULL,
                receipt_date DATE NOT NULL,
                reference_doc VARCHAR(100) NULL,
                status ENUM('draft', 'processed', 'cancelled') DEFAULT 'processed',
                received_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY unique_grn_number (company_id, receipt_no),
                CONSTRAINT fk_grn_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_grn_po FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE SET NULL,
                CONSTRAINT fk_grn_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchasing_goods_receipt_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                goods_receipt_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                warehouse_id BIGINT UNSIGNED NOT NULL,
                ordered_quantity DECIMAL(18, 4) DEFAULT 0.0000,
                received_quantity DECIMAL(18, 4) NOT NULL,
                unit_cost DECIMAL(18, 4) DEFAULT 0.0000,
                CONSTRAINT fk_gri_grn FOREIGN KEY (goods_receipt_id) REFERENCES purchasing_goods_receipts(id) ON DELETE CASCADE,
                CONSTRAINT fk_gri_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
                CONSTRAINT fk_gri_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // 5. Purchase Invoices (Bills)
        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_invoices (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_id BIGINT UNSIGNED NOT NULL,
                branch_id BIGINT UNSIGNED NULL,
                invoice_no VARCHAR(50) NOT NULL,
                supplier_bill_no VARCHAR(100) NOT NULL,
                supplier_id BIGINT UNSIGNED NOT NULL,
                purchase_order_id BIGINT UNSIGNED NULL,
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
                UNIQUE KEY unique_pi_number (company_id, invoice_no),
                CONSTRAINT fk_pi_company FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                CONSTRAINT fk_pi_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        $this->connection->statement("
            CREATE TABLE IF NOT EXISTS purchase_invoice_items (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                purchase_invoice_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                description VARCHAR(255) NULL,
                quantity DECIMAL(18, 4) NOT NULL,
                unit_price DECIMAL(18, 4) NOT NULL,
                discount_amount DECIMAL(18, 4) DEFAULT 0.0000,
                tax_amount DECIMAL(18, 4) DEFAULT 0.0000,
                total DECIMAL(18, 4) NOT NULL,
                warehouse_id BIGINT UNSIGNED NULL,
                CONSTRAINT fk_pii_pi FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoices(id) ON DELETE CASCADE,
                CONSTRAINT fk_pii_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    }

    public function down(): void
    {
        $this->connection->statement("DROP TABLE IF EXISTS purchase_invoice_items;");
        $this->connection->statement("DROP TABLE IF EXISTS purchase_invoices;");
        $this->connection->statement("DROP TABLE IF EXISTS purchasing_goods_receipt_items;");
        $this->connection->statement("DROP TABLE IF EXISTS purchasing_goods_receipts;");
        $this->connection->statement("DROP TABLE IF EXISTS purchase_order_items;");
        $this->connection->statement("DROP TABLE IF EXISTS purchase_orders;");
        $this->connection->statement("DROP TABLE IF EXISTS purchase_requisition_items;");
        $this->connection->statement("DROP TABLE IF EXISTS purchase_requisitions;");
        $this->connection->statement("DROP TABLE IF EXISTS suppliers;");
    }
}