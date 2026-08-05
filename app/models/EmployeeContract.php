<?php
// app/models/EmployeeContract.php

class EmployeeContract extends Model {
    
    public function __construct() {
        parent::__construct();
        // نستخدم الجدول الشامل للعقود
        $this->table = 'contracts';
    }

    /**
     * جلب جميع عقود الموظفين مع بيانات الموظف
     */
    public function getAllContracts(): array {
        $sql = "SELECT c.*, e.name as employee_name, e.position 
                FROM {$this->table} c 
                JOIN employees e ON c.party_id = e.id 
                WHERE c.party_type = 'employee' 
                ORDER BY c.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * إنشاء عقد موظف جديد
     */
    public function createContract(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (contract_number, title, party_type, party_id, start_date, end_date, value, status, created_at) 
                VALUES 
                (:contract_number, :title, 'employee', :party_id, :start_date, :end_date, :value, :status, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':contract_number', $data['contract_number']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':party_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':value', $data['value']);
        $this->db->bind(':status', $data['status']);
        
        return $this->db->execute();
    }

    /**
     * تحديث حالة العقد (مثلاً: إنهاء العقد)
     */
    public function updateStatus(int $id, string $status): bool {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id AND party_type = 'employee'";
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}