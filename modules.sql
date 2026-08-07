-- modules.sql

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- مسح الجداول القديمة إن وجدت لإعادة بنائها نظيفة
-- --------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`, `documents`, `contracts`, `support_tickets`;
DROP TABLE IF EXISTS `opportunities`, `followups`, `leads`;
DROP TABLE IF EXISTS `payments`, `payroll_details`, `payrolls`, `sanctions`, `leave_requests`, `leave_types`, `employee_appraisals`, `employee_advances`, `attendance`;
DROP TABLE IF EXISTS `fixed_assets`, `expenses`, `expense_categories`;
DROP TABLE IF EXISTS `journal_lines`, `journal_entries`, `chart_of_accounts`, `financial_transactions`, `treasuries`;
DROP TABLE IF EXISTS `stock_adjustments`, `sales_return_items`, `sales_returns`, `purchase_return_items`, `purchase_returns`;
DROP TABLE IF EXISTS `purchase_order_items`, `purchase_orders`, `purchase_request_items`, `purchase_requests`;
DROP TABLE IF EXISTS `invoice_items`, `invoices`, `sales_order_items`, `sales_orders`;
DROP TABLE IF EXISTS `project_tasks`, `projects`, `products`, `categories`, `suppliers`, `customers`, `employees`, `users`;


-- --------------------------------------------------------
-- 1. النظام والمستخدمين
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','manager','editor','employee') DEFAULT 'employee',
  `phone` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إدخال حساب المدير (admin / admin)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('المدير العام', 'admin@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


-- --------------------------------------------------------
-- 2. الموارد البشرية (HR)
-- --------------------------------------------------------
CREATE TABLE `employees` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `position` VARCHAR(100) DEFAULT NULL,
  `salary` DECIMAL(10,2) DEFAULT '0.00',
  `hire_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 3. المبيعات والعملاء (CRM & Sales)
-- --------------------------------------------------------
CREATE TABLE `customers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `type` ENUM('individual', 'company') DEFAULT 'individual',
  `balance` DECIMAL(15,2) DEFAULT '0.00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 4. المشتريات والموردين (Purchasing)
-- --------------------------------------------------------
CREATE TABLE `suppliers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `contact_person` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `balance` DECIMAL(15,2) DEFAULT '0.00',
  `type` ENUM('company','individual') DEFAULT 'company',
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 5. المستودعات والأصناف (Inventory)
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `category_id` INT(11) DEFAULT NULL,
  `name` VARCHAR(150) NOT NULL,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `barcode` VARCHAR(100) DEFAULT NULL,
  `unit` VARCHAR(50) DEFAULT 'قطعة',
  `quantity` INT(11) DEFAULT '0',
  `reorder_point` INT(11) DEFAULT '5',
  `track_batches` TINYINT(1) DEFAULT '0',
  `price` DECIMAL(10,2) DEFAULT '0.00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 6. الفواتير والطلبات (Sales Orders & Invoices)
-- --------------------------------------------------------
CREATE TABLE `sales_orders` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT(11) NOT NULL,
    `order_date` DATE NOT NULL,
    `status` ENUM('draft', 'confirmed', 'invoiced', 'canceled') DEFAULT 'draft',
    `total_amount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
    `notes` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sales_order_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `sales_orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `invoices` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` INT(11) DEFAULT NULL,
  `customer_name` VARCHAR(100) DEFAULT NULL,
  `total_amount` DECIMAL(15,2) DEFAULT '0.00',
  `sales_rep_id` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `invoice_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL DEFAULT '1',
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 7. المشتريات وطلباتها (Purchases)
-- --------------------------------------------------------
CREATE TABLE `purchase_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `request_number` VARCHAR(50) NOT NULL UNIQUE,
    `requested_by` INT(11) NOT NULL,
    `request_date` DATE NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'ordered') DEFAULT 'pending',
    `notes` TEXT,
    `approved_by` INT(11) DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchase_request_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `request_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL,
    `estimated_price` DECIMAL(10,2) DEFAULT '0.00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`request_id`) REFERENCES `purchase_requests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchase_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `po_number` VARCHAR(50) NOT NULL UNIQUE,
  `supplier_id` INT(11) NOT NULL,
  `total_amount` DECIMAL(15,2) DEFAULT '0.00',
  `status` ENUM('pending','approved','ordered','delivered','cancelled') DEFAULT 'pending',
  `notes` TEXT DEFAULT NULL,
  `received_date` DATE DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchase_order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `po_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `quantity_ordered` INT(11) NOT NULL,
  `quantity_received` INT(11) DEFAULT '0',
  `unit_price` DECIMAL(10,2) NOT NULL,
  `total` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 8. المرتجعات (Returns) وتسويات المخزون
-- --------------------------------------------------------
CREATE TABLE `sales_returns` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `return_number` VARCHAR(50) NOT NULL UNIQUE,
    `invoice_id` INT(11) NOT NULL,
    `total_refund` DECIMAL(15,2) DEFAULT '0.00',
    `reason` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sales_return_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `return_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`return_id`) REFERENCES `sales_returns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchase_returns` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `return_number` VARCHAR(50) NOT NULL UNIQUE,
    `po_id` INT(11) DEFAULT NULL,
    `supplier_id` INT(11) NOT NULL,
    `total_refund` DECIMAL(15,2) DEFAULT '0.00',
    `reason` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `purchase_return_items` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `return_id` INT(11) NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `subtotal` DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`return_id`) REFERENCES `purchase_returns`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `stock_adjustments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `reference_no` VARCHAR(50) NOT NULL UNIQUE,
    `date` DATE NOT NULL,
    `type` ENUM('addition', 'subtraction', 'damage', 'loss') NOT NULL,
    `product_id` INT(11) NOT NULL,
    `quantity` INT(11) NOT NULL,
    `notes` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 9. الخزائن والبنوك (Treasury & Finance)
-- --------------------------------------------------------
CREATE TABLE `treasuries` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('cash', 'bank') DEFAULT 'cash',
    `account_number` VARCHAR(50) DEFAULT NULL,
    `current_balance` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `treasuries` (`id`, `name`, `type`, `current_balance`) VALUES (1, 'الصندوق الرئيسي', 'cash', 0.00);

CREATE TABLE `financial_transactions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `treasury_id` INT(11) NOT NULL,
    `transaction_type` ENUM('receipt', 'payment') NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `transaction_date` DATE NOT NULL,
    `reference` VARCHAR(100) DEFAULT NULL,
    `description` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`treasury_id`) REFERENCES `treasuries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 10. المحاسبة والقيود (Accounting)
-- --------------------------------------------------------
CREATE TABLE `chart_of_accounts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(20) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `type` ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    `parent_id` INT(11) DEFAULT NULL,
    `balance` DECIMAL(15,2) DEFAULT '0.00',
    `is_active` TINYINT(1) DEFAULT '1',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `journal_entries` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `entry_number` VARCHAR(50) NOT NULL UNIQUE,
    `entry_date` DATE NOT NULL,
    `description` TEXT NOT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` INT(11) DEFAULT NULL,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `journal_lines` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `journal_entry_id` INT(11) NOT NULL,
    `account_id` INT(11) NOT NULL,
    `debit` DECIMAL(15,2) DEFAULT '0.00',
    `credit` DECIMAL(15,2) DEFAULT '0.00',
    `description` TEXT,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 11. المصروفات والأصول (Expenses & Assets)
-- --------------------------------------------------------
CREATE TABLE `expense_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `expense_categories` (`id`, `name`) VALUES (1, 'إيجارات'), (2, 'كهرباء ومياه'), (3, 'صيانة'), (4, 'أجور ورواتب'), (5, 'مصروفات تسويق');

CREATE TABLE `expenses` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `category_id` INT(11) NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `expense_date` DATE NOT NULL,
    `reference_no` VARCHAR(50) DEFAULT NULL,
    `notes` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `expense_categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `fixed_assets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `asset_tag` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `purchase_date` DATE NOT NULL,
    `purchase_cost` DECIMAL(15,2) NOT NULL,
    `salvage_value` DECIMAL(15,2) DEFAULT '0.00',
    `useful_life_years` INT(11) NOT NULL,
    `location` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active', 'maintenance', 'disposed', 'sold') DEFAULT 'active',
    `notes` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 12. مكملات الموارد البشرية (HR Extensions)
-- --------------------------------------------------------
CREATE TABLE `attendance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `date` DATE NOT NULL,
    `check_in` TIME DEFAULT NULL,
    `check_out` TIME DEFAULT NULL,
    `status` ENUM('present', 'absent', 'late', 'leave') DEFAULT 'present',
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employee_advances` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `date` DATE NOT NULL,
    `reason` TEXT,
    `deduction_month` INT(11) NOT NULL,
    `deduction_year` INT(11) NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'deducted') DEFAULT 'pending',
    `approved_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employee_appraisals` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `evaluation_date` DATE NOT NULL,
    `performance_score` INT(11) DEFAULT '0',
    `behavior_score` INT(11) DEFAULT '0',
    `attendance_score` INT(11) DEFAULT '0',
    `total_score` DECIMAL(5,2) DEFAULT '0.00',
    `grade` VARCHAR(50) DEFAULT NULL,
    `evaluator_id` INT(11) NOT NULL,
    `comments` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `leave_types` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `is_paid` TINYINT(1) DEFAULT '1',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `leave_types` (`name`, `is_paid`) VALUES 
('إجازة سنوية', 1), ('إجازة مرضية', 1), ('إجازة بدون راتب', 0);

CREATE TABLE `leave_requests` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `leave_type_id` INT(11) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `reason` TEXT DEFAULT NULL,
    `status` ENUM('pending','approved','rejected') DEFAULT 'pending',
    `approved_by` INT(11) DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `sanctions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `employee_id` INT(11) NOT NULL,
    `type` ENUM('warning','deduction') NOT NULL,
    `amount` DECIMAL(10,2) DEFAULT '0.00',
    `date` DATE NOT NULL,
    `reason` TEXT NOT NULL,
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `payrolls` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `reference_no` VARCHAR(50) NOT NULL UNIQUE,
    `month` TINYINT(2) NOT NULL,
    `year` YEAR(4) NOT NULL,
    `total_employees` INT(11) NOT NULL DEFAULT '0',
    `total_net_amount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
    `status` ENUM('draft','approved','paid') DEFAULT 'approved',
    `created_by` INT(11) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `payroll_details` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `payroll_id` INT(11) NOT NULL,
    `employee_id` INT(11) NOT NULL,
    `employee_name` VARCHAR(100) NOT NULL,
    `base_salary` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `deductions` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `bonuses` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    `net_salary` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
    PRIMARY KEY (`id`),
    FOREIGN KEY (`payroll_id`) REFERENCES `payrolls`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `payments` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `reference_id` INT(11) NOT NULL,
    `reference_type` ENUM('invoice','purchase_order') NOT NULL,
    `amount` DECIMAL(15,2) NOT NULL,
    `payment_method` VARCHAR(50) DEFAULT 'cash',
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 13. إدارة علاقات العملاء والمشاريع (CRM & Projects)
-- --------------------------------------------------------
CREATE TABLE `leads` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `company` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `source` VARCHAR(50) DEFAULT NULL,
    `status` ENUM('new', 'contacted', 'qualified', 'lost') DEFAULT 'new',
    `assigned_to` INT(11) DEFAULT NULL,
    `notes` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `followups` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `lead_id` INT(11) NOT NULL,
    `type` ENUM('call', 'meeting', 'email') DEFAULT 'call',
    `scheduled_date` DATETIME NOT NULL,
    `status` ENUM('pending', 'completed', 'canceled') DEFAULT 'pending',
    `notes` TEXT,
    `created_by` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `opportunities` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `customer_id` INT(11) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `stage` ENUM('qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost') DEFAULT 'qualification',
    `estimated_value` DECIMAL(15,2) DEFAULT '0.00',
    `probability` INT(11) DEFAULT '50',
    `expected_close_date` DATE DEFAULT NULL,
    `assigned_to` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `projects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `customer_id` INT(11) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `status` ENUM('planning','active','on_hold','completed','cancelled') DEFAULT 'planning',
  `budget` DECIMAL(15,2) DEFAULT '0.00',
  `project_manager` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `project_tasks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `project_id` INT(11) NOT NULL,
  `title` VARCHAR(150) NOT NULL,
  `start_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `progress` INT(11) DEFAULT '0',
  `assigned_to` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------
-- 14. النظام والأرشيف والمهام العامة (System & DMS)
-- --------------------------------------------------------
CREATE TABLE `support_tickets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `ticket_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT(11) DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    `assigned_to` INT(11) DEFAULT NULL,
    `resolved_at` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `contracts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `contract_number` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `party_type` ENUM('customer', 'supplier', 'employee') NOT NULL,
    `party_id` INT(11) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `value` DECIMAL(15,2) DEFAULT '0.00',
    `status` ENUM('draft', 'active', 'expired', 'terminated') DEFAULT 'draft',
    `file_path` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `documents` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` INT(11) NOT NULL,
    `folder_path` VARCHAR(255) DEFAULT '/',
    `uploaded_by` INT(11) NOT NULL,
    `access_level` ENUM('public', 'private', 'role_based') DEFAULT 'private',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `activity_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) DEFAULT NULL,
    `action` VARCHAR(50) NOT NULL,
    `module` VARCHAR(100) NOT NULL,
    `record_id` INT(11) DEFAULT NULL,
    `description` TEXT,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- تفعيل التحقق من المفاتيح الأجنبية مرة أخرى
SET FOREIGN_KEY_CHECKS = 1;