-- ============================================
-- إنشاء قاعدة البيانات
-- ============================================
CREATE DATABASE IF NOT EXISTS `erp_system` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `erp_system`;

-- ============================================
-- الجداول الأساسية (Core Tables)
-- ============================================

-- 1. الأقسام
CREATE TABLE IF NOT EXISTS `departments` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. التصنيفات
CREATE TABLE IF NOT EXISTS `categories` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. المنتجات
CREATE TABLE IF NOT EXISTS `products` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    category_id INT DEFAULT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 4. العملاء
CREATE TABLE IF NOT EXISTS `customers` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    type ENUM('individual', 'company') DEFAULT 'individual',
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. الموردين
CREATE TABLE IF NOT EXISTS `suppliers` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    type ENUM('individual', 'company') DEFAULT 'company',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. الموظفين
CREATE TABLE IF NOT EXISTS `employees` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    position VARCHAR(100) DEFAULT NULL,
    salary DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    department_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_employees_department FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- 7. المصروفات
CREATE TABLE IF NOT EXISTS `expenses` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    category VARCHAR(50) DEFAULT 'أخرى',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 8. الفواتير
CREATE TABLE IF NOT EXISTS `invoices` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number VARCHAR(50) NOT NULL UNIQUE,
    customer_id INT DEFAULT NULL,
    customer_name VARCHAR(200) DEFAULT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoices_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- 9. أصناف الفواتير
CREATE TABLE IF NOT EXISTS `invoice_items` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_invoice_items_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    CONSTRAINT fk_invoice_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE
);

-- 10. أوامر الشراء (الأساسية)
CREATE TABLE IF NOT EXISTS `purchase_orders` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50) NOT NULL UNIQUE,
    supplier_id INT DEFAULT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    status ENUM('pending', 'approved', 'ordered', 'delivered', 'cancelled', 'rejected') DEFAULT 'pending',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_purchase_orders_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT
);

-- 11. المدفوعات
CREATE TABLE IF NOT EXISTS `payments` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_type ENUM('invoice', 'purchase_order', 'salary', 'other') DEFAULT 'invoice',
    reference_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    method ENUM('cash', 'bank_transfer', 'check', 'card') DEFAULT 'cash',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 12. المستخدمين
CREATE TABLE IF NOT EXISTS `users` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    phone VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 13. الإعدادات
CREATE TABLE IF NOT EXISTS `settings` (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================
-- الجداول الإضافية (وحدات جديدة)
-- ============================================

-- 14. سجل التدقيق
CREATE TABLE IF NOT EXISTS `audit_logs` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT DEFAULT NULL,
    old_data JSON DEFAULT NULL,
    new_data JSON DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 15. الإشعارات
CREATE TABLE IF NOT EXISTS `notifications` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 16. أنواع الإجازات
CREATE TABLE IF NOT EXISTS `leave_types` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    days_per_year INT NOT NULL DEFAULT 0,
    is_paid BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 17. طلبات الإجازات
CREATE TABLE IF NOT EXISTS `leave_requests` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    reason TEXT DEFAULT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    approved_by INT DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_leave_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    CONSTRAINT fk_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    CONSTRAINT fk_leave_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 18. الحضور والانصراف
CREATE TABLE IF NOT EXISTS `attendance` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    check_in TIME DEFAULT NULL,
    check_out TIME DEFAULT NULL,
    status ENUM('present', 'absent', 'late', 'leave') DEFAULT 'absent',
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attendance_employee FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (employee_id, date)
);

-- 19. شجرة الحسابات
CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    type ENUM('asset', 'liability', 'equity', 'revenue', 'expense') NOT NULL,
    parent_id INT DEFAULT NULL,
    balance DECIMAL(15,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_account_parent FOREIGN KEY (parent_id) REFERENCES chart_of_accounts(id) ON DELETE CASCADE
);

-- 20. القيود اليومية
CREATE TABLE IF NOT EXISTS `journal_entries` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entry_number VARCHAR(20) NOT NULL UNIQUE,
    entry_date DATE NOT NULL,
    description TEXT DEFAULT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id INT DEFAULT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_journal_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 21. سطور القيود
CREATE TABLE IF NOT EXISTS `journal_lines` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    journal_entry_id INT NOT NULL,
    account_id INT NOT NULL,
    debit DECIMAL(15,2) DEFAULT 0.00,
    credit DECIMAL(15,2) DEFAULT 0.00,
    description TEXT DEFAULT NULL,
    CONSTRAINT fk_journal_line_entry FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
    CONSTRAINT fk_journal_line_account FOREIGN KEY (account_id) REFERENCES chart_of_accounts(id) ON DELETE RESTRICT
);

-- 22. أصناف أوامر الشراء (متقدمة)
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL DEFAULT 1,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_po_items_po FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_po_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- 23. المستودعات
CREATE TABLE IF NOT EXISTS `warehouses` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    address TEXT DEFAULT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 24. مخزون المستودعات
CREATE TABLE IF NOT EXISTS `warehouse_stock` (
    product_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, warehouse_id),
    CONSTRAINT fk_ws_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_ws_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE
);

-- 25. نقل المخزون
CREATE TABLE IF NOT EXISTS `stock_transfers` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transfer_number VARCHAR(20) NOT NULL UNIQUE,
    from_warehouse_id INT NOT NULL,
    to_warehouse_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
    requested_by INT NOT NULL,
    approved_by INT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME DEFAULT NULL,
    CONSTRAINT fk_transfer_from_wh FOREIGN KEY (from_warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transfer_to_wh FOREIGN KEY (to_warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transfer_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    CONSTRAINT fk_transfer_requested_by FOREIGN KEY (requested_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 26. الفرص التجارية (CRM)
CREATE TABLE IF NOT EXISTS `opportunities` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    stage ENUM('qualification', 'needs_analysis', 'proposal', 'negotiation', 'closed_won', 'closed_lost') DEFAULT 'qualification',
    estimated_value DECIMAL(10,2) DEFAULT 0.00,
    probability INT DEFAULT 0,
    expected_close_date DATE DEFAULT NULL,
    assigned_to INT DEFAULT NULL,
    closed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_opportunity_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    CONSTRAINT fk_opportunity_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- 27. المتابعات (CRM)
CREATE TABLE IF NOT EXISTS `follow_ups` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    opportunity_id INT DEFAULT NULL,
    customer_id INT DEFAULT NULL,
    type ENUM('call', 'meeting', 'email', 'task') NOT NULL,
    subject VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    scheduled_at DATETIME NOT NULL,
    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    completed_at DATETIME DEFAULT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_followup_opportunity FOREIGN KEY (opportunity_id) REFERENCES opportunities(id) ON DELETE CASCADE,
    CONSTRAINT fk_followup_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    CONSTRAINT fk_followup_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- 28. الأصول الثابتة
CREATE TABLE IF NOT EXISTS `fixed_assets` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    asset_code VARCHAR(50) NOT NULL UNIQUE,
    category VARCHAR(100) DEFAULT NULL,
    purchase_date DATE DEFAULT NULL,
    purchase_price DECIMAL(10,2) NOT NULL,
    salvage_value DECIMAL(10,2) DEFAULT 0.00,
    useful_life_years INT DEFAULT 5,
    depreciation_method ENUM('straight_line', 'declining_balance') DEFAULT 'straight_line',
    current_value DECIMAL(10,2) DEFAULT 0.00,
    assigned_to INT DEFAULT NULL,
    location VARCHAR(200) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_asset_employee FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL
);

-- 29. المشاريع
CREATE TABLE IF NOT EXISTS `projects` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    customer_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    status ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') DEFAULT 'planning',
    budget DECIMAL(15,2) DEFAULT 0.00,
    project_manager INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_project_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    CONSTRAINT fk_project_manager FOREIGN KEY (project_manager) REFERENCES employees(id) ON DELETE SET NULL
);

-- 30. مهام المشاريع
CREATE TABLE IF NOT EXISTS `project_tasks` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    assigned_to INT DEFAULT NULL,
    due_date DATE DEFAULT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('todo', 'in_progress', 'review', 'done') DEFAULT 'todo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_task_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_task_assigned FOREIGN KEY (assigned_to) REFERENCES employees(id) ON DELETE SET NULL
);

-- ============================================
-- إضافة أعمدة جديدة إلى الجداول الموجودة
-- ============================================
ALTER TABLE `purchase_orders` ADD COLUMN `expected_delivery_date` DATE DEFAULT NULL;
ALTER TABLE `purchase_orders` ADD COLUMN `received_date` DATE DEFAULT NULL;
ALTER TABLE `products` ADD COLUMN `warehouse_id` INT DEFAULT NULL;
ALTER TABLE `products` ADD CONSTRAINT fk_product_warehouse FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL;

-- ============================================
-- إدراج البيانات الأولية (Basic Data)
-- ============================================

-- الأقسام
INSERT INTO `departments` (`name`) VALUES
    ('تقنية المعلومات'),
    ('المالية'),
    ('الموارد البشرية'),
    ('التسويق'),
    ('الصيانة');

-- التصنيفات
INSERT INTO `categories` (`name`) VALUES
    ('إلكترونيات'),
    ('برمجيات'),
    ('أجهزة'),
    ('نقل'),
    ('صيانة'),
    ('مصاريف');

-- أنواع الإجازات
INSERT INTO `leave_types` (`name`, `days_per_year`, `is_paid`) VALUES
    ('سنوية', 21, 1),
    ('مرضية', 10, 1),
    ('طارئة', 5, 1);

-- المستودعات
INSERT INTO `warehouses` (`name`, `code`, `is_main`) VALUES
    ('المستودع الرئيسي', 'WH-001', 1),
    ('مستودع فرع الرياض', 'WH-002', 0);

-- المستخدم الافتراضي (كلمة المرور: admin)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
    ('مدير النظام', 'admin@system.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- الإعدادات الأساسية
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
    ('company_name', 'شركتي للتجارة'),
    ('company_email', 'info@company.com'),
    ('company_phone', '0501234567'),
    ('currency', 'ر.س'),
    ('tax_rate', '15'),
    ('app_version', '2.0.0');

-- حسابات المحاسبة الأساسية
INSERT INTO `chart_of_accounts` (`code`, `name`, `type`) VALUES
    ('1010', 'الخزينة', 'asset'),
    ('2010', 'رأس المال', 'equity');

-- ============================================
-- بيانات تجريبية (اختيارية)
-- ============================================

-- منتجات تجريبية
INSERT INTO `products` (`sku`, `name`, `category_id`, `quantity`, `price`, `warehouse_id`) VALUES
    ('PRD-202501-001', 'لابتوب Dell Inspiron 15', 1, 10, 3500.00, 1),
    ('PRD-202501-002', 'شاشة Samsung 24 بوصة', 3, 5, 1200.00, 1),
    ('PRD-202501-003', 'طابعة HP LaserJet', 3, 3, 2500.00, 1),
    ('PRD-202501-004', 'خدمة استشارية برمجية', 2, 999, 500.00, 2),
    ('PRD-202501-005', 'جهاز توجيه (راوتر) Cisco', 1, 7, 800.00, 1);

-- عملاء تجريبيون
INSERT INTO `customers` (`name`, `email`, `phone`, `type`) VALUES
    ('أحمد محمد', 'ahmed@example.com', '0512345678', 'individual'),
    ('شركة الأمل', 'info@almal.com', '0551234567', 'company');

-- موردون تجريبيون
INSERT INTO `suppliers` (`name`, `contact_person`, `phone`, `email`, `type`) VALUES
    ('شركة التقنية', 'خالد سالم', '0567891234', 'tech@supplier.com', 'company'),
    ('مؤسسة الوسيط', 'فهد العبدالله', '0543219876', 'waseet@supplier.com', 'company');

-- موظفون تجريبيون
INSERT INTO `employees` (`name`, `email`, `phone`, `position`, `salary`, `department_id`) VALUES
    ('أحمد محمد علي', 'ahmed@company.com', '0512345678', 'مدير تقنية', 15000, 1),
    ('سارة خالد', 'sara@company.com', '0551234567', 'محاسب', 12000, 2),
    ('فاطمة الشريطة', 'fatima@company.com', '0551234567', 'مساعد إداري', 8000, 3);

-- مصروفات تجريبية
INSERT INTO `expenses` (`description`, `amount`, `category`) VALUES
    ('فاتورة كهرباء يناير', 4500.00, 'كهرباء وماء'),
    ('إيجار المكتب', 8000.00, 'إيجار');

-- توزيع الكميات الأولية في المستودعات
INSERT INTO `warehouse_stock` (`product_id`, `warehouse_id`, `quantity`) VALUES
    (1, 1, 10),
    (2, 1, 5),
    (3, 1, 3),
    (4, 2, 999),
    (5, 1, 7);