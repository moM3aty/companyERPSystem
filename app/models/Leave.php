<?php
// app/models/Leave.php

class Leave extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'leave_requests';
    }

    /**
     * جلب جميع طلبات الإجازة مع بيانات الموظف ونوع الإجازة
     */
    public function getAllRequests(): array {
        $sql = "SELECT lr.*, 
                       e.name as employee_name, 
                       lt.name as leave_type_name 
                FROM {$this->table} lr 
                LEFT JOIN employees e ON lr.employee_id = e.id 
                LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id 
                ORDER BY lr.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب أنواع الإجازات المتاحة
     */
    public function getLeaveTypes(): array {
        $this->db->query("SELECT * FROM leave_types ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * إنشاء طلب إجازة جديد
     */
    public function createRequest(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, leave_type_id, start_date, end_date, reason, status) 
                VALUES (:employee_id, :leave_type_id, :start_date, :end_date, :reason, 'pending')";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':leave_type_id', $data['leave_type_id'], PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':reason', $data['reason']);
        
        return $this->db->execute();
    }

    /**
     * تحديث حالة طلب الإجازة (موافقة / رفض)
     */
    public function updateStatus(int $id, string $status, int $adminId): bool {
        $sql = "UPDATE {$this->table} 
                SET status = :status, approved_by = :admin_id, approved_at = NOW() 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':status', $status);
        $this->db->bind(':admin_id', $adminId, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
}