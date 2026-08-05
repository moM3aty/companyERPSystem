<?php
// app/models/Advance.php

class Advance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_advances';
    }

    /**
     * جلب جميع السلف مع بيانات الموظف
     */
    public function getAllAdvances(): array {
        $sql = "SELECT a.*, e.name as employee_name, e.salary 
                FROM {$this->table} a 
                LEFT JOIN employees e ON a.employee_id = e.id 
                ORDER BY a.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * إنشاء طلب سلفة جديد
     */
    public function createAdvance(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, amount, date, reason, deduction_month, deduction_year, status) 
                VALUES (:employee_id, :amount, :date, :reason, :deduction_month, :deduction_year, 'pending')";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':deduction_month', $data['deduction_month'], PDO::PARAM_INT);
        $this->db->bind(':deduction_year', $data['deduction_year'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    /**
     * تحديث حالة السلفة (موافقة / رفض)
     */
    public function updateStatus(int $id, string $status, int $adminId): bool {
        $sql = "UPDATE {$this->table} 
                SET status = :status, approved_by = :admin_id 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':admin_id', $adminId, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}