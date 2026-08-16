-- =================================================================================
-- نظام ERP Pro الشامل - هيكل قاعدة البيانات الاحترافي (Enterprise Edition V5)
-- مبني بالكامل على أفضل الممارسات المعمارية (Multi-Tenant, Multi-Branch, Audit Trails, Soft Deletes)
-- =================================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";

-- =================================================================================
-- 1. CORE / ADMINISTRATION MODULE
-- =================================================================================

CREATE TABLE `companies` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_code` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `legal_name` VARCHAR(255) DEFAULT NULL,
  `tax_number` VARCHAR(100) DEFAULT NULL,
  `commercial_register` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `timezone` VARCHAR(100) DEFAULT 'Asia/Riyadh',
  `status` ENUM('active', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `branches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `manager_employee_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `unique_company_branch` (`company_id`, `branch_code`),
  CONSTRAINT `fk_branch_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `departments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `parent_department_id` BIGINT UNSIGNED DEFAULT NULL,
  `department_code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `manager_employee_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_dept_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dept_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_dept_parent` FOREIGN KEY (`parent_department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `document_sequences` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `document_type` VARCHAR(50) NOT NULL, -- e.g., 'INV', 'PO', 'SO', 'JV'
  `prefix` VARCHAR(20) NOT NULL,
  `current_number` BIGINT UNSIGNED NOT NULL DEFAULT 1,
  `year` YEAR DEFAULT NULL,
  `month` TINYINT(2) DEFAULT NULL,
  `padding` INT DEFAULT 5, -- e.g., 5 means 00001
  UNIQUE KEY `unique_sequence` (`company_id`, `branch_id`, `document_type`, `year`, `month`),
  CONSTRAINT `fk_seq_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `value_type` VARCHAR(50) DEFAULT 'string',
  `module` VARCHAR(50) DEFAULT 'core',
  `is_encrypted` TINYINT(1) DEFAULT 0,
  UNIQUE KEY `unique_setting` (`company_id`, `setting_key`),
  CONSTRAINT `fk_set_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 2. USERS & SECURITY MODULE (AUTH)
-- =================================================================================

CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `employee_id` BIGINT UNSIGNED DEFAULT NULL,
  `language` VARCHAR(10) DEFAULT 'ar',
  `timezone` VARCHAR(100) DEFAULT 'Asia/Riyadh',
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_user_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `is_system_role` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_role_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `module` VARCHAR(50) NOT NULL,
  `resource` VARCHAR(50) NOT NULL,
  `action` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  UNIQUE KEY `unique_perm` (`module`, `resource`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
  `role_id` BIGINT UNSIGNED NOT NULL,
  `permission_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_roles` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 3. CRM (CUSTOMERS & LEADS)
-- =================================================================================

CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `customer_code` VARCHAR(50) NOT NULL,
  `customer_type` ENUM('individual', 'company') DEFAULT 'company',
  `name` VARCHAR(255) NOT NULL,
  `legal_name` VARCHAR(255) DEFAULT NULL,
  `tax_number` VARCHAR(100) DEFAULT NULL,
  `commercial_register` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `mobile` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country_id` BIGINT UNSIGNED DEFAULT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `payment_term_id` BIGINT UNSIGNED DEFAULT NULL,
  `credit_limit` DECIMAL(15,2) DEFAULT 0.00,
  `sales_rep_id` BIGINT UNSIGNED DEFAULT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `unique_comp_cust` (`company_id`, `customer_code`),
  CONSTRAINT `fk_cust_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cust_rep` FOREIGN KEY (`sales_rep_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customer_contacts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `job_title` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `mobile` VARCHAR(50) DEFAULT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  CONSTRAINT `fk_contact_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `leads` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `company` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `source` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('new','contacted','qualified','lost') DEFAULT 'new',
  `assigned_to` BIGINT UNSIGNED DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_lead_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 4. PURCHASING (SUPPLIERS)
-- =================================================================================

CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `supplier_code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `legal_name` VARCHAR(255) DEFAULT NULL,
  `tax_number` VARCHAR(100) DEFAULT NULL,
  `commercial_register` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `country_id` BIGINT UNSIGNED DEFAULT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `payment_term_id` BIGINT UNSIGNED DEFAULT NULL,
  `credit_limit` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `unique_comp_supp` (`company_id`, `supplier_code`),
  CONSTRAINT `fk_supp_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `supplier_contacts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `job_title` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `mobile` VARCHAR(50) DEFAULT NULL,
  `is_primary` TINYINT(1) DEFAULT 0,
  CONSTRAINT `fk_contact_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 5. INVENTORY & PRODUCTS MODULE
-- =================================================================================

CREATE TABLE `product_categories` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_pcat_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pcat_parent` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `units_of_measure` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `code` VARCHAR(20) NOT NULL, -- e.g., PCS, KG, M
  `name` VARCHAR(50) NOT NULL,
  `symbol` VARCHAR(10) DEFAULT NULL,
  `decimal_places` TINYINT DEFAULT 0,
  CONSTRAINT `fk_uom_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `sku` VARCHAR(100) NOT NULL,
  `barcode` VARCHAR(100) DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `category_id` BIGINT UNSIGNED DEFAULT NULL,
  `unit_id` BIGINT UNSIGNED DEFAULT NULL,
  `product_type` ENUM('stock', 'service', 'consumable', 'asset') DEFAULT 'stock',
  `track_inventory` TINYINT(1) DEFAULT 1,
  `track_serial` TINYINT(1) DEFAULT 0,
  `track_batch` TINYINT(1) DEFAULT 0,
  `is_sellable` TINYINT(1) DEFAULT 1,
  `is_purchasable` TINYINT(1) DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `cost_method` ENUM('FIFO', 'LIFO', 'Average', 'Standard') DEFAULT 'Average',
  `standard_cost` DECIMAL(15,4) DEFAULT 0.00,
  `sales_price` DECIMAL(15,4) DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `unique_comp_sku` (`company_id`, `sku`),
  CONSTRAINT `fk_prod_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prod_cat_v2` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_prod_uom` FOREIGN KEY (`unit_id`) REFERENCES `units_of_measure` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `warehouse_type` VARCHAR(50) DEFAULT 'internal',
  `manager_employee_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_wh_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wh_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `warehouse_locations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `parent_location_id` BIGINT UNSIGNED DEFAULT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `location_type` VARCHAR(50) DEFAULT 'bin', -- aisle, rack, bin
  CONSTRAINT `fk_loc_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_loc_parent` FOREIGN KEY (`parent_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_stock` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `location_id` BIGINT UNSIGNED DEFAULT NULL,
  `quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.00,
  `reserved_quantity` DECIMAL(15,4) NOT NULL DEFAULT 0.00,
  `available_quantity` DECIMAL(15,4) GENERATED ALWAYS AS (quantity - reserved_quantity) STORED,
  `average_cost` DECIMAL(15,4) DEFAULT 0.00,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_stock_loc` (`product_id`, `warehouse_id`, `location_id`),
  CONSTRAINT `fk_stock_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_stock_loc` FOREIGN KEY (`location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- دفتر الأستاذ للمخزون (Inventory Ledger - الأهم للحركات)
CREATE TABLE `inventory_transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `transaction_no` VARCHAR(50) NOT NULL,
  `transaction_type` ENUM('PURCHASE_RECEIPT','SALES_ISSUE','TRANSFER_OUT','TRANSFER_IN','ADJUSTMENT_IN','ADJUSTMENT_OUT','RETURN_IN','RETURN_OUT','OPENING_BALANCE') NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `location_id` BIGINT UNSIGNED DEFAULT NULL,
  `batch_id` BIGINT UNSIGNED DEFAULT NULL,
  `serial_id` BIGINT UNSIGNED DEFAULT NULL,
  `quantity` DECIMAL(15,4) NOT NULL, -- موجب أو سالب حسب نوع الحركة
  `unit_cost` DECIMAL(15,4) NOT NULL DEFAULT 0.00,
  `total_cost` DECIMAL(15,4) GENERATED ALWAYS AS (quantity * unit_cost) STORED,
  `reference_type` VARCHAR(50) DEFAULT NULL, -- e.g., 'invoice', 'po', 'transfer'
  `reference_id` BIGINT UNSIGNED DEFAULT NULL,
  `transaction_date` DATETIME NOT NULL,
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_invtx_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invtx_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invtx_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_inventory_transactions_product ON inventory_transactions(product_id);
CREATE INDEX idx_inventory_transactions_date ON inventory_transactions(transaction_date);

CREATE TABLE `batches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `batch_number` VARCHAR(100) NOT NULL,
  `manufacture_date` DATE DEFAULT NULL,
  `expiry_date` DATE DEFAULT NULL,
  `supplier_id` BIGINT UNSIGNED DEFAULT NULL,
  CONSTRAINT `fk_batch_prod_v2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_batch_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `serial_numbers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `serial_number` VARCHAR(100) NOT NULL,
  `status` ENUM('available','sold','returned','damaged') DEFAULT 'available',
  `current_warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  `current_location_id` BIGINT UNSIGNED DEFAULT NULL,
  `purchase_id` BIGINT UNSIGNED DEFAULT NULL,
  `sale_id` BIGINT UNSIGNED DEFAULT NULL,
  UNIQUE KEY `unique_serial_product` (`product_id`, `serial_number`),
  CONSTRAINT `fk_serial_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 6. FINANCE & ACCOUNTING MODULE (THE CORE)
-- =================================================================================

CREATE TABLE `chart_of_accounts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `account_code` VARCHAR(50) NOT NULL,
  `account_name` VARCHAR(150) NOT NULL,
  `account_type` ENUM('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `normal_balance` ENUM('Debit','Credit') NOT NULL,
  `level` INT DEFAULT 1,
  `is_control_account` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  UNIQUE KEY `unique_comp_acc_code` (`company_id`, `account_code`),
  CONSTRAINT `fk_coa_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_coa_parent_v2` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fiscal_years` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL, -- e.g. FY-2026
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` ENUM('open', 'closed') DEFAULT 'open',
  CONSTRAINT `fk_fy_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fiscal_periods` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `fiscal_year_id` BIGINT UNSIGNED NOT NULL,
  `period_no` TINYINT NOT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `status` ENUM('open', 'closed') DEFAULT 'open',
  CONSTRAINT `fk_fp_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entries` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `entry_no` VARCHAR(50) NOT NULL,
  `entry_date` DATE NOT NULL,
  `reference_type` VARCHAR(50) DEFAULT NULL,
  `reference_id` BIGINT UNSIGNED DEFAULT NULL,
  `description` TEXT NOT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `exchange_rate` DECIMAL(15,6) DEFAULT 1.000000,
  `status` ENUM('draft', 'posted', 'voided') DEFAULT 'draft',
  `posted_by` BIGINT UNSIGNED DEFAULT NULL,
  `posted_at` DATETIME DEFAULT NULL,
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_comp_je` (`company_id`, `entry_no`),
  CONSTRAINT `fk_je_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `journal_entry_lines` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `journal_entry_id` BIGINT UNSIGNED NOT NULL,
  `account_id` BIGINT UNSIGNED NOT NULL,
  `cost_center_id` BIGINT UNSIGNED DEFAULT NULL,
  `department_id` BIGINT UNSIGNED DEFAULT NULL,
  `project_id` BIGINT UNSIGNED DEFAULT NULL,
  `debit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `credit` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `description` TEXT DEFAULT NULL,
  CONSTRAINT `fk_jel_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jel_acc` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- تطبيق Constraint على مستوى الجدول في البرمجة لضمان SUM(debit) = SUM(credit)

CREATE TABLE `taxes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `tax_type` ENUM('VAT', 'Withholding', 'Custom', 'Exempt', 'Zero') DEFAULT 'VAT',
  `rate` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `sales_account_id` BIGINT UNSIGNED DEFAULT NULL,
  `purchase_account_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  CONSTRAINT `fk_tax_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tax_sales_acc` FOREIGN KEY (`sales_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cost_centers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `parent_id` BIGINT UNSIGNED DEFAULT NULL,
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `department_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  CONSTRAINT `fk_cc_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 7. SALES MODULE
-- =================================================================================

CREATE TABLE `sales_quotations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `quotation_no` VARCHAR(50) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `quotation_date` DATE NOT NULL,
  `valid_until` DATE NOT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `sales_rep_id` BIGINT UNSIGNED DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `discount_total` DECIMAL(15,2) DEFAULT 0.00,
  `tax_total` DECIMAL(15,2) DEFAULT 0.00,
  `grand_total` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('draft','sent','accepted','rejected') DEFAULT 'draft',
  `notes` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_comp_qte` (`company_id`, `quotation_no`),
  CONSTRAINT `fk_qte_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_quotation_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `quotation_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `unit_price` DECIMAL(15,4) NOT NULL,
  `discount_percent` DECIMAL(5,2) DEFAULT 0.00,
  `discount_amount` DECIMAL(15,2) DEFAULT 0.00,
  `tax_percent` DECIMAL(5,2) DEFAULT 0.00,
  `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) NOT NULL,
  `warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  CONSTRAINT `fk_qtei_qte` FOREIGN KEY (`quotation_id`) REFERENCES `sales_quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_no` VARCHAR(50) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `quotation_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_date` DATE NOT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `payment_term_id` BIGINT UNSIGNED DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `discount_total` DECIMAL(15,2) DEFAULT 0.00,
  `tax_total` DECIMAL(15,2) DEFAULT 0.00,
  `grand_total` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('draft','approved','processing','shipped','delivered','cancelled') DEFAULT 'draft',
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_comp_so` (`company_id`, `order_no`),
  CONSTRAINT `fk_so_cust` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_order_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `sales_order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `unit_price` DECIMAL(15,4) NOT NULL,
  `discount` DECIMAL(15,2) DEFAULT 0.00,
  `tax` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) NOT NULL,
  `warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  CONSTRAINT `fk_soi_so` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales_invoices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `invoice_no` VARCHAR(50) NOT NULL,
  `customer_id` BIGINT UNSIGNED NOT NULL,
  `sales_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `invoice_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `discount_total` DECIMAL(15,2) DEFAULT 0.00,
  `tax_total` DECIMAL(15,2) DEFAULT 0.00,
  `grand_total` DECIMAL(15,2) DEFAULT 0.00,
  `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
  `remaining_amount` DECIMAL(15,2) GENERATED ALWAYS AS (grand_total - paid_amount) STORED,
  `status` ENUM('draft','posted','paid','partial','void') DEFAULT 'draft',
  `journal_entry_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_comp_inv` (`company_id`, `invoice_no`),
  CONSTRAINT `fk_inv_cust_v2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_inv_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_sales_invoices_customer ON sales_invoices(customer_id);

CREATE TABLE `sales_invoice_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `quantity` DECIMAL(15,4) NOT NULL,
  `unit_price` DECIMAL(15,4) NOT NULL,
  `discount_amount` DECIMAL(15,2) DEFAULT 0.00,
  `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
  `total` DECIMAL(15,2) NOT NULL,
  `warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  CONSTRAINT `fk_invi_inv` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 8. PURCHASING MODULE
-- =================================================================================

CREATE TABLE `purchase_requisitions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `request_no` VARCHAR(50) NOT NULL,
  `requested_by` BIGINT UNSIGNED NOT NULL,
  `department_id` BIGINT UNSIGNED DEFAULT NULL,
  `request_date` DATE NOT NULL,
  `required_date` DATE NOT NULL,
  `priority` VARCHAR(20) DEFAULT 'normal',
  `status` ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
  CONSTRAINT `fk_prq_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_orders` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `po_no` VARCHAR(50) NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `request_id` BIGINT UNSIGNED DEFAULT NULL,
  `order_date` DATE NOT NULL,
  `expected_date` DATE DEFAULT NULL,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `payment_term_id` BIGINT UNSIGNED DEFAULT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `discount_total` DECIMAL(15,2) DEFAULT 0.00,
  `tax_total` DECIMAL(15,2) DEFAULT 0.00,
  `grand_total` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('draft','approved','sent','received','partial','cancelled') DEFAULT 'draft',
  `created_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_comp_po` (`company_id`, `po_no`),
  CONSTRAINT `fk_po_supp_v2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `purchase_invoices` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `invoice_no` VARCHAR(50) NOT NULL,
  `supplier_id` BIGINT UNSIGNED NOT NULL,
  `purchase_order_id` BIGINT UNSIGNED DEFAULT NULL,
  `invoice_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `subtotal` DECIMAL(15,2) DEFAULT 0.00,
  `discount_total` DECIMAL(15,2) DEFAULT 0.00,
  `tax_total` DECIMAL(15,2) DEFAULT 0.00,
  `grand_total` DECIMAL(15,2) DEFAULT 0.00,
  `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
  `remaining_amount` DECIMAL(15,2) GENERATED ALWAYS AS (grand_total - paid_amount) STORED,
  `status` ENUM('draft','posted','paid','partial','void') DEFAULT 'draft',
  `journal_entry_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_pinv_supp` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 9. HR & PAYROLL MODULE
-- =================================================================================

CREATE TABLE `employees` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `branch_id` BIGINT UNSIGNED DEFAULT NULL,
  `employee_code` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `middle_name` VARCHAR(100) DEFAULT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `national_id` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `department_id` BIGINT UNSIGNED DEFAULT NULL,
  `job_id` BIGINT UNSIGNED DEFAULT NULL,
  `manager_id` BIGINT UNSIGNED DEFAULT NULL,
  `hire_date` DATE DEFAULT NULL,
  `employment_status` VARCHAR(50) DEFAULT 'active',
  `salary` DECIMAL(15,2) DEFAULT 0.00,
  `currency_id` BIGINT UNSIGNED DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_emp_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `attendance` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `attendance_date` DATE NOT NULL,
  `check_in` TIME DEFAULT NULL,
  `check_out` TIME DEFAULT NULL,
  `worked_hours` DECIMAL(5,2) DEFAULT 0.00,
  `late_minutes` INT DEFAULT 0,
  `overtime_hours` DECIMAL(5,2) DEFAULT 0.00,
  `status` VARCHAR(50) DEFAULT 'present',
  CONSTRAINT `fk_att_emp_v2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll_runs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `period_start` DATE NOT NULL,
  `period_end` DATE NOT NULL,
  `pay_date` DATE NOT NULL,
  `status` ENUM('draft', 'approved', 'paid') DEFAULT 'draft',
  CONSTRAINT `fk_pr_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payroll_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `payroll_run_id` BIGINT UNSIGNED NOT NULL,
  `employee_id` BIGINT UNSIGNED NOT NULL,
  `basic_salary` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `allowances` DECIMAL(15,2) DEFAULT 0.00,
  `overtime` DECIMAL(15,2) DEFAULT 0.00,
  `bonuses` DECIMAL(15,2) DEFAULT 0.00,
  `deductions` DECIMAL(15,2) DEFAULT 0.00,
  `tax` DECIMAL(15,2) DEFAULT 0.00,
  `social_insurance` DECIMAL(15,2) DEFAULT 0.00,
  `net_salary` DECIMAL(15,2) GENERATED ALWAYS AS ((basic_salary + allowances + overtime + bonuses) - (deductions + tax + social_insurance)) STORED,
  CONSTRAINT `fk_prec_run` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prec_emp` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 10. ASSETS & PROJECTS
-- =================================================================================

CREATE TABLE `fixed_assets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `asset_code` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `category_id` BIGINT UNSIGNED DEFAULT NULL,
  `purchase_date` DATE NOT NULL,
  `purchase_cost` DECIMAL(15,2) NOT NULL,
  `residual_value` DECIMAL(15,2) DEFAULT 0.00,
  `useful_life` INT NOT NULL COMMENT 'In months or years',
  `depreciation_method` VARCHAR(50) DEFAULT 'Straight Line',
  `current_value` DECIMAL(15,2) NOT NULL,
  `department_id` BIGINT UNSIGNED DEFAULT NULL,
  `location_id` BIGINT UNSIGNED DEFAULT NULL,
  `status` ENUM('active','maintenance','disposed') DEFAULT 'active',
  CONSTRAINT `fk_fa_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `projects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `project_code` VARCHAR(50) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `customer_id` BIGINT UNSIGNED DEFAULT NULL,
  `manager_id` BIGINT UNSIGNED DEFAULT NULL,
  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,
  `budget` DECIMAL(15,2) DEFAULT 0.00,
  `actual_cost` DECIMAL(15,2) DEFAULT 0.00,
  `status` ENUM('planning','active','completed','on_hold') DEFAULT 'planning',
  CONSTRAINT `fk_proj_company_v2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================================================================================
-- 11. SYSTEM: WORKFLOW, AUDIT, NOTIFICATIONS
-- =================================================================================

CREATE TABLE `approval_workflows` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `module` VARCHAR(50) NOT NULL,
  `document_type` VARCHAR(50) NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  CONSTRAINT `fk_aw_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_steps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `workflow_id` BIGINT UNSIGNED NOT NULL,
  `step_order` INT NOT NULL,
  `approver_type` VARCHAR(50) NOT NULL, -- e.g. 'role', 'user', 'manager'
  `approver_id` BIGINT UNSIGNED NOT NULL,
  `min_amount` DECIMAL(15,2) DEFAULT 0.00,
  `max_amount` DECIMAL(15,2) DEFAULT 999999999.99,
  CONSTRAINT `fk_as_workflow` FOREIGN KEY (`workflow_id`) REFERENCES `approval_workflows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- أهم جدول للرقابة (Audit Logs) يدعم JSONB
CREATE TABLE `audit_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `company_id` BIGINT UNSIGNED NOT NULL,
  `user_id` BIGINT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(50) NOT NULL, -- CREATE, UPDATE, DELETE
  `module` VARCHAR(100) NOT NULL,
  `table_name` VARCHAR(100) NOT NULL,
  `record_id` BIGINT UNSIGNED NOT NULL,
  `old_values` JSON DEFAULT NULL,
  `new_values` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_al_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `reference_type` VARCHAR(50) DEFAULT NULL,
  `reference_id` BIGINT UNSIGNED DEFAULT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `read_at` DATETIME DEFAULT NULL,
  CONSTRAINT `fk_notif_user_v2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;