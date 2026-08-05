<?php
// app/models/Expense.php

class Expense extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'expenses';
    }

    /**
     * جلب جميع المصروفات مع اسم التصنيف والمستخدم
     */
    public function getAllExpenses(): array {
        $sql = "SELECT e.*, 
                       c.name as category_name, 
                       u.name as recorded_by_name 
                FROM {$this->table} e 
                LEFT JOIN expense_categories c ON e.category_id = c.id 
                LEFT JOIN users u ON e.recorded_by = u.id 
                ORDER BY e.expense_date DESC, e.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * حساب إجمالي المصروفات
     */
    public function getTotalExpenses(): float {
        $sql = "SELECT SUM(amount) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }

    /**
     * جلب تصنيفات المصروفات
     */
    public function getCategories(): array {
        $this->db->query("SELECT * FROM expense_categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * إضافة مصروف جديد
     */
    public function createExpense(array $data): bool {
        $sql = "INSERT INTO {$this->table} (category_id, amount, expense_date, reference_no, notes, recorded_by, created_at) 
                VALUES (:category_id, :amount, :expense_date, :reference_no, :notes, :recorded_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':category_id', $data['category_id'], PDO::PARAM_INT);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':expense_date', $data['expense_date']);
        $this->db->bind(':reference_no', $data['reference_no']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':recorded_by', $data['recorded_by'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}