-- --------------------------------------------------------
-- نظام ERP Pro - هيكل قاعدة البيانات وبيانات البداية
-- الترميز: UTF-8 (يدعم اللغة العربية بشكل كامل)
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `erp_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `erp_system`;

-- 1. جدول المستخدمين (Users)
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','employee') DEFAULT 'employee',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إدخال حساب المدير الافتراضي (كلمة المرور المشفرة لـ: admin)
-- في هذا المثال نستخدم هاش bcrypt لكلمة admin
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('مدير النظام', 'admin@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');


-- 2. جدول الموظفين (Employees)
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT '0.00',
  `hire_date` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 3. جدول العملاء (Customers)
CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 4. جدول الموردين (Suppliers)
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `balance` decimal(15,2) DEFAULT '0.00' COMMENT 'الرصيد الدائن للمورد',
  `type` enum('company','individual') DEFAULT 'company',
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 5. جدول التصنيفات (Categories)
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 6. جدول المنتجات والمخزون (Products)
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `sku` varchar(50) NOT NULL UNIQUE,
  `quantity` int(11) DEFAULT '0',
  `price` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 7. جدول فواتير المبيعات (Invoices)
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- تفاصيل فواتير المبيعات (Invoice Items)
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 8. جدول أوامر الشراء (Purchase Orders)
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL UNIQUE,
  `supplier_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) DEFAULT '0.00',
  `status` enum('pending','approved','ordered','delivered','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- تفاصيل أوامر الشراء (Purchase Order Items)
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `quantity_received` int(11) DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL,
  `total` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`po_id`) REFERENCES `purchase_orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 29. المشاريع
CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
-- ============================================
-- إضافة جداول للوحدات الجديدة (15 Modules)
-- ============================================

-- وحدة خدمة العملاء (Help Desk Tickets)
CREATE TABLE IF NOT EXISTS `support_tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_number` VARCHAR(50) NOT NULL UNIQUE,
    `customer_id` INT DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    `status` ENUM('open', 'in_progress', 'resolved', 'closed') DEFAULT 'open',
    `assigned_to` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `resolved_at` DATETIME DEFAULT NULL,
    CONSTRAINT fk_ticket_customer FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
    CONSTRAINT fk_ticket_assigned FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL
);

-- وحدة إدارة العقود (Contracts)
CREATE TABLE IF NOT EXISTS `contracts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `contract_number` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `party_type` ENUM('customer', 'supplier', 'employee') NOT NULL,
    `party_id` INT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `value` DECIMAL(15,2) DEFAULT 0.00,
    `status` ENUM('draft', 'active', 'expired', 'terminated') DEFAULT 'draft',
    `file_path` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- وحدة إدارة الوثائق (DMS)
CREATE TABLE IF NOT EXISTS `documents` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `file_name` VARCHAR(255) NOT NULL,
    `file_type` VARCHAR(50) NOT NULL,
    `file_size` INT NOT NULL COMMENT 'Size in bytes',
    `folder_path` VARCHAR(255) DEFAULT '/',
    `uploaded_by` INT NOT NULL,
    `access_level` ENUM('public', 'private', 'role_based') DEFAULT 'private',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_doc_uploaded_by FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- ============================================
-- إدراج البيانات الأولية (Basic Data)
-- ============================================
  `is_paid` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `leave_types` (`name`, `is_paid`) VALUES 
('إجازة سنوية', 1), ('إجازة مرضية', 1), ('إجازة طارئة', 1), ('إجازة بدون راتب', 0);

CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types`(`id`),
  FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 12. جدول الجزاءات (Sanctions)
CREATE TABLE IF NOT EXISTS `sanctions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `type` enum('warning','deduction') NOT NULL,
  `amount` decimal(10,2) DEFAULT '0.00',
  `date` date NOT NULL,
  `reason` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 13. مسيرات الرواتب (Payrolls)
CREATE TABLE IF NOT EXISTS `payrolls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(50) NOT NULL UNIQUE,
  `month` tinyint(2) NOT NULL,
  `year` year(4) NOT NULL,
  `total_employees` int(11) NOT NULL DEFAULT '0',
  `total_net_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','approved','paid') DEFAULT 'approved',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `payroll_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payroll_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `base_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonuses` decimal(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`payroll_id`) REFERENCES `payrolls`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 14. جدول المدفوعات/المقبوضات (Payments) - لتسجيل تسديدات الموردين أو العملاء
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_id` int(11) NOT NULL COMMENT 'معرف الفاتورة أو أمر الشراء',
  `reference_type` enum('invoice','purchase_order') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- نهاية هيكل قاعدة البيانات
-- --------------------------------------------------------