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
        
        $this->db->query("SELECT COUNT(*) as count FROM employees WHERE company_id = :cid AND employment_status = 'Active'");
        $this->db->bind(':cid', $companyId);
        $activeEmployees = $this->db->single()->count ?? 0;

        $this->db->query("SELECT SUM(basic_salary) as total_basic, 
                                 SUM(housing_allowance + transport_allowance + other_allowances) as total_allowances 
                          FROM employees 
                          WHERE company_id = :cid AND employment_status = 'Active'");
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
        $this->ensureTableExists('purchase_invoices');
        $companyId = Session::get('company_id') ?: 1;

        $sql = "SELECT COUNT(id) as total_orders, 
                SUM(grand_total) as total_purchases 
                FROM purchase_invoices 
                WHERE company_id = :cid AND DATE(created_at) BETWEEN :start AND :end";
        $this->db->query($sql);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        $purchases = clone $this->db->single();

        return [
            'total_orders' => $purchases->total_orders ?? 0,
            'total_purchases' => $purchases->total_purchases ?? 0,
            'total_returns' => 0,
            'total_returned_amount' => 0,
            'net_purchases' => $purchases->total_purchases ?? 0
        ];
    }

    public function getSupplierReport($startDate, $endDate) {
        $this->ensureTableExists('purchase_invoices');
        
        $sql = "SELECT s.company_name as supplier_name, COUNT(pi.id) as order_count, SUM(pi.grand_total) as total_amount 
                FROM purchase_invoices pi
                JOIN suppliers s ON pi.supplier_id = s.id
                WHERE pi.company_id = :cid 
                  AND DATE(pi.created_at) BETWEEN :start AND :end
                GROUP BY s.id, s.company_name 
                ORDER BY total_amount DESC 
                LIMIT 10";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':start', $startDate . ' 00:00:00');
        $this->db->bind(':end', $endDate . ' 23:59:59');
        return $this->db->resultSet();
    }

    // ==========================================
    // 4. القوائم المالية (Income Statement & Balance Sheet)
    // ==========================================
    public function getIncomeStatement($startDate, $endDate) {
        $companyId = Session::get('company_id') ?: 1;
        
        // إجمالي الإيرادات (دائن - مدين)
        $this->db->query("SELECT SUM(credit - debit) as total_revenue FROM journal_lines jl JOIN accounting_accounts a ON jl.account_id = a.id JOIN accounting_journals je ON jl.journal_id = je.id WHERE a.account_type = 'Revenue' AND je.company_id = :cid AND je.date BETWEEN :start AND :end");
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $revenue = $this->db->single()->total_revenue ?? 0;

        // إجمالي المصروفات (مدين - دائن)
        $this->db->query("SELECT SUM(debit - credit) as total_expense FROM journal_lines jl JOIN accounting_accounts a ON jl.account_id = a.id JOIN accounting_journals je ON jl.journal_id = je.id WHERE a.account_type = 'Expense' AND je.company_id = :cid AND je.date BETWEEN :start AND :end");
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':start', $startDate);
        $this->db->bind(':end', $endDate);
        $expenses = $this->db->single()->total_expense ?? 0;

        $netIncome = $revenue - $expenses;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net_income' => $netIncome
        ];
    }
    
    public function getAccountBalancesByType($type) {
        $this->db->query("SELECT * FROM accounting_accounts WHERE account_type = :type AND (company_id = :cid OR company_id IS NULL) ORDER BY account_code ASC");
        $this->db->bind(':type', $type);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    // ==========================================
    // 5. الحماية
    // ==========================================
    private function ensureTableExists($tableName) {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$tableName}` (`id` int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`))");
            $this->db->execute();
        } catch (Exception $e) {}
    }
}