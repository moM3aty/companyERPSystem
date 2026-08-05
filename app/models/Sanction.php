<?php
// app/models/Sanction.php

class Sanction extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sanctions';
    }

    /**
     * جلب جميع الجزاءات مع بيانات الموظف والمدير
     * @return array
     */
    public function getAllSanctions(): array {
        $sql = "SELECT s.*, 
                       e.name as employee_name, 
                       u.name as created_by_name 
                FROM {$this->table} s 
                LEFT JOIN employees e ON s.employee_id = e.id 
                LEFT JOIN users u ON s.created_by = u.id 
                ORDER BY s.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * حساب إجمالي الخصومات المالية
     * @return float
     */
    public function getTotalDeductions(): float {
        $sql = "SELECT SUM(amount) as total FROM {$this->table} WHERE type = 'deduction'";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }

    /**
     * حساب عدد الإنذارات
     * @return int
     */
    public function getWarningsCount(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE type = 'warning'";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    /**
     * إضافة جزاء جديد
     * @param array $data
     * @return bool
     */
    public function createSanction(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, type, amount, date, reason, created_by, created_at) 
                VALUES (:employee_id, :type, :amount, :date, :reason, :created_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}