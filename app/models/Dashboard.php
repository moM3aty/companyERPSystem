<?php
// app/models/Dashboard.php

class Dashboard extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getFinanceMetrics() {
        $cid = Session::get('company_id') ?: 1;
        $metrics = [];

        // 1. Cash & Bank Balances
        $this->db->query("SELECT type, SUM(current_balance) as total FROM treasuries WHERE company_id = :cid GROUP BY type");
        $this->db->bind(':cid', $cid);
        $treasuries = $this->db->resultSet();
        $metrics['cash_balance'] = 0;
        $metrics['bank_balance'] = 0;
        foreach ($treasuries as $t) {
            if ($t->type == 'Bank') $metrics['bank_balance'] += $t->total;
            else $metrics['cash_balance'] += $t->total; // Cash & Petty Cash
        }

        // 2. Accounts Receivable (AR) & Accounts Payable (AP)
        $this->db->query("SELECT SUM(current_balance) as ar FROM customers WHERE company_id = :cid");
        $this->db->bind(':cid', $cid);
        $metrics['accounts_receivable'] = $this->db->single()->ar ?? 0;

        $this->db->query("SELECT SUM(current_balance) as ap FROM suppliers WHERE company_id = :cid");
        $this->db->bind(':cid', $cid);
        $metrics['accounts_payable'] = $this->db->single()->ap ?? 0;

        // 3. Current Month Sales, Purchases, Expenses
        $this->db->query("SELECT SUM(grand_total) as sales, SUM(tax_amount) as sales_tax FROM sales_invoices WHERE company_id = :cid AND MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE())");
        $this->db->bind(':cid', $cid);
        $salesData = $this->db->single();
        $metrics['monthly_sales'] = $salesData->sales ?? 0;
        $salesTax = $salesData->sales_tax ?? 0;

        $this->db->query("SELECT SUM(grand_total) as purchases, SUM(tax_amount) as purch_tax FROM purchase_invoices WHERE company_id = :cid AND MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE())");
        $this->db->bind(':cid', $cid);
        $purchData = $this->db->single();
        $metrics['monthly_purchases'] = $purchData->purchases ?? 0;
        $purchTax = $purchData->purch_tax ?? 0;

        $this->db->query("SELECT SUM(amount) as expenses FROM expenses WHERE company_id = :cid AND MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())");
        $this->db->bind(':cid', $cid);
        $metrics['monthly_expenses'] = $this->db->single()->expenses ?? 0;

        // 4. Profitability
        $metrics['gross_profit'] = $metrics['monthly_sales'] - $metrics['monthly_purchases']; // Simplified COGS for dashboard
        $metrics['net_profit'] = $metrics['gross_profit'] - $metrics['monthly_expenses'];

        // 5. VAT/Tax Payable (Net VAT)
        $metrics['vat_payable'] = $salesTax - $purchTax;

        // 6. Outstanding & Overdue
        $this->db->query("SELECT COUNT(id) as count, SUM(grand_total - amount_paid) as outstanding FROM sales_invoices WHERE company_id = :cid AND payment_status != 'Paid'");
        $this->db->bind(':cid', $cid);
        $outSales = $this->db->single();
        $metrics['outstanding_invoices_count'] = $outSales->count ?? 0;
        $metrics['outstanding_invoices_amount'] = $outSales->outstanding ?? 0;

        $this->db->query("SELECT SUM(grand_total - amount_paid) as overdue FROM sales_invoices WHERE company_id = :cid AND payment_status != 'Paid' AND due_date < CURDATE()");
        $this->db->bind(':cid', $cid);
        $metrics['overdue_customer_payments'] = $this->db->single()->overdue ?? 0;

        // 7. Upcoming Payments (Next 7 days)
        $this->db->query("SELECT SUM(grand_total - amount_paid) as upcoming FROM purchase_invoices WHERE company_id = :cid AND payment_status != 'Paid' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
        $this->db->bind(':cid', $cid);
        $metrics['upcoming_payments'] = $this->db->single()->upcoming ?? 0;

        return $metrics;
    }

    public function getMonthlyCashFlow() {
        $cid = Session::get('company_id') ?: 1;
        // يجلب بيانات آخر 6 أشهر للتدفق النقدي
        $sql = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as month, payment_type, SUM(amount) as total 
                FROM payments 
                WHERE company_id = :cid AND payment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                GROUP BY month, payment_type 
                ORDER BY month ASC";
        $this->db->query($sql);
        $this->db->bind(':cid', $cid);
        $results = $this->db->resultSet();

        $cashFlow = ['labels' => [], 'in' => [], 'out' => []];
        $temp = [];
        
        foreach($results as $r) {
            $m = $r->month;
            if(!isset($temp[$m])) $temp[$m] = ['in' => 0, 'out' => 0];
            if($r->payment_type == 'In') $temp[$m]['in'] = $r->total;
            if($r->payment_type == 'Out') $temp[$m]['out'] = $r->total;
        }

        foreach($temp as $month => $data) {
            $cashFlow['labels'][] = date('M Y', strtotime($month . '-01'));
            $cashFlow['in'][] = $data['in'];
            $cashFlow['out'][] = $data['out'];
        }

        return $cashFlow;
    }
}