<?php
// app/models/Report.php

class Report extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    // ==========================================
    // 1. تقارير المبيعات (Sales)
    // ==========================================
    public function getSalesReport($startDate, $endDate) {
        $sql = "SELECT DATE(created_at) as sale_date, COUNT(id) as total_invoices, SUM(total_amount) as total_sales 
                FROM invoices 
                WHERE company_id = :cid AND DATE(created_at) BETWEEN :start AND :end
                GROUP BY DATE(created_at)
                ORDER BY DATE(created_at) DESC";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        return $this->db->resultSet();
    }

    public function getTopSellingProducts($startDate, $endDate, $limit = 5) {
        $sql = "SELECT p.name, p.sku, SUM(ii.quantity) as total_qty, SUM(ii.subtotal) as total_revenue
                FROM invoice_items ii
                JOIN invoices i ON ii.invoice_id = i.id
                JOIN products p ON ii.product_id = p.id
                WHERE i.company_id = :cid AND DATE(i.created_at) BETWEEN :start AND :end
                GROUP BY p.id, p.name, p.sku
                ORDER BY total_qty DESC
                LIMIT " . (int)$limit;
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        return $this->db->resultSet();
    }

    // ==========================================
    // 2. تقارير الموارد البشرية (HR)
    // ==========================================
    public function getHrReport($startDate, $endDate) {
        $companyId = Session::get('company_id') ?: 1;
        
        $this->ensureTableExists('employees');
        $this->ensureTableExists('employee_contracts');
        
        $this->db->query("SELECT COUNT(*) as count FROM employees WHERE company_id = :cid AND status = 'active'");
        $this->db->bind(':cid', $companyId);
        $activeEmployees = $this->db->single()->count ?? 0;

        $this->db->query("SELECT SUM(basic_salary) as total_basic, SUM(allowances) as total_allowances 
                          FROM employee_contracts 
                          WHERE company_id = :cid AND status = 'active'");
        $this->db->bind(':cid', $companyId);
        $payroll = $this->db->single();
        
        $totalBasic = $payroll->total_basic ?? 0;
        $totalAllowances = $payroll->total_allowances ?? 0;

        return [
            'active_employees' => $activeEmployees,
            'total_basic_salary' => $totalBasic,
            'total_allowances' => $totalAllowances,
            'estimated_payroll' => $totalBasic + $totalAllowances
        ];
    }

    // ==========================================
    // 3. تقارير المشتريات (Purchases)
    // ==========================================
    public function getPurchasesReport($startDate, $endDate) {
        // حماية مسبقة للجداول والأعمدة
        $this->ensureTableExists('purchases');
        $this->ensureTableExists('purchase_returns');
        
        $companyId = Session::get('company_id') ?: 1;

        // إجمالي المشتريات (يستثني الملغي)
        $sql = "SELECT COUNT(id) as total_orders, 
                SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as total_purchases 
                FROM purchases 
                WHERE company_id = :cid AND DATE(created_at) BETWEEN :start AND :end";
        $this->db->query($sql);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        $purchases = clone $this->db->single();

        // إجمالي المرتجعات
        $sqlRet = "SELECT COUNT(id) as total_returns, 
                   SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as total_returned_amount 
                   FROM purchase_returns 
                   WHERE company_id = :cid AND return_date BETWEEN :start AND :end";
        $this->db->query($sqlRet);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $returns = clone $this->db->single();

        return [
            'total_orders' => $purchases->total_orders ?? 0,
            'total_purchases' => $purchases->total_purchases ?? 0,
            'total_returns' => $returns->total_returns ?? 0,
            'total_returned_amount' => $returns->total_returned_amount ?? 0,
            'net_purchases' => ($purchases->total_purchases ?? 0) - ($returns->total_returned_amount ?? 0)
        ];
    }

    public function getSupplierReport($startDate, $endDate) {
        $this->ensureTableExists('purchases');
        
        $sql = "SELECT supplier_name, COUNT(id) as order_count, SUM(total_amount) as total_amount 
                FROM purchases 
                WHERE company_id = :cid AND status != 'cancelled' 
                  AND supplier_name IS NOT NULL AND supplier_name != '' 
                  AND DATE(created_at) BETWEEN :start AND :end
                GROUP BY supplier_name 
                ORDER BY total_amount DESC 
                LIMIT 10";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        return $this->db->resultSet();
    }

    // ==========================================
    // 4. دالة الحماية والترقية الديناميكية 
    // ==========================================
    private function ensureTableExists($tableName) {
        try {
            // 1. إنشاء الجدول إن لم يكن موجوداً
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`))");
            $this->db->execute();
            
            // 2. فحص وتوليد الأعمدة المهمة لتقارير المشتريات لتجنب الـ Fatal Errors
            if (in_array($tableName, ['purchases', 'purchase_returns'])) {
                
                $columnsNeeded = [
                    'company_id'    => 'INT DEFAULT 1',
                    'total_amount'  => 'DECIMAL(15,2) DEFAULT 0.00',
                    'status'        => "VARCHAR(50) DEFAULT 'approved'",
                    'supplier_name' => "VARCHAR(255) DEFAULT NULL"
                ];

                foreach($columnsNeeded as $col => $def) {
                    $this->db->query("SHOW COLUMNS FROM `{$tableName}` LIKE '{$col}'");
                    if (empty($this->db->resultSet())) {
                        $this->db->query("ALTER TABLE `{$tableName}` ADD `{$col}` {$def}");
                        $this->db->execute();
                    }
                }

                // إضافة أعمدة التواريخ بناءً على نوع الجدول
                if ($tableName === 'purchases') {
                    $this->db->query("SHOW COLUMNS FROM `{$tableName}` LIKE 'created_at'");
                    if (empty($this->db->resultSet())) {
                        $this->db->query("ALTER TABLE `{$tableName}` ADD `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP");
                        $this->db->execute();
                    }
                } else {
                    $this->db->query("SHOW COLUMNS FROM `{$tableName}` LIKE 'return_date'");
                    if (empty($this->db->resultSet())) {
                        $this->db->query("ALTER TABLE `{$tableName}` ADD `return_date` DATE DEFAULT NULL");
                        $this->db->execute();
                    }
                }
            }
        } catch (Exception $e) {
            // صمت لتجنب توقف النظام في حال وجود أخطاء في الصلاحيات
        }
    }
}