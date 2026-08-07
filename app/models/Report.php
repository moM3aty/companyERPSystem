<?php
// app/models/Report.php

class Report extends Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function getSalesByMonth(int $year): array {
        $sql = "SELECT MONTH(created_at) as month, SUM(total_amount) as total_sales 
                FROM invoices 
                WHERE YEAR(created_at) = :year 
                GROUP BY MONTH(created_at) 
                ORDER BY MONTH(created_at)";
        $this->db->query($sql);
        $this->db->bind(':year', $year, PDO::PARAM_INT);
        $results = $this->db->resultSet();
        
        $monthlySales = array_fill(1, 12, 0.0);
        foreach ($results as $row) {
            $monthlySales[(int)$row->month] = (float)$row->total_sales;
        }
        return $monthlySales;
    }

    public function getExpensesByCategory(int $year): array {
        $sql = "SELECT c.name as category_name, SUM(e.amount) as total_amount 
                FROM expenses e 
                JOIN expense_categories c ON e.category_id = c.id 
                WHERE YEAR(e.expense_date) = :year 
                GROUP BY c.id";
        $this->db->query($sql);
        $this->db->bind(':year', $year, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getInventoryValuation(): array {
        $sql = "SELECT c.name as category_name, 
                       COUNT(p.id) as products_count, 
                       SUM(p.quantity) as total_items, 
                       SUM(p.quantity * p.price) as total_value 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                GROUP BY p.category_id";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getTopCustomers(int $limit = 5): array {
        $sql = "SELECT customer_name, SUM(total_amount) as total_purchases, COUNT(id) as invoices_count 
                FROM invoices 
                GROUP BY customer_id, customer_name 
                ORDER BY total_purchases DESC 
                LIMIT " . $limit;
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    public function getTopSellingProducts(int $limit = 5): array {
        $sql = "SELECT p.name, SUM(ii.quantity) as qty_sold, SUM(ii.subtotal) as total_revenue
                FROM invoice_items ii
                JOIN products p ON ii.product_id = p.id
                GROUP BY ii.product_id, p.name
                ORDER BY qty_sold DESC
                LIMIT " . $limit;
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    public function getDetailedSalesReport(string $startDate, string $endDate): array {
        $sql = "SELECT i.invoice_number, i.customer_name, i.total_amount, i.created_at, u.name as sales_rep 
                FROM invoices i 
                LEFT JOIN users u ON i.sales_rep_id = u.id 
                WHERE DATE(i.created_at) BETWEEN :start_date AND :end_date 
                ORDER BY i.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate . ' 00:00:00');
        $this->db->bind(':end_date', $endDate . ' 23:59:59');
        return $this->db->resultSet();
    }
}