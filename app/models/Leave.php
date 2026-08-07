<?php
// app/models/Leave.php

class Leave extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'leave_requests';
    }

    public function getAllRequests(): array {
        $sql = "SELECT lr.*, 
                       e.name as employee_name, 
                       lt.name as leave_type_name,
                       u.name as approved_by_name
                FROM {$this->table} lr 
                LEFT JOIN employees e ON lr.employee_id = e.id 
                LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id 
                LEFT JOIN users u ON lr.approved_by = u.id
                ORDER BY lr.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getRequestById(int $id): ?object {
        $sql = "SELECT lr.* FROM {$this->table} lr WHERE lr.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getLeaveTypes(): array {
        $this->db->query("SELECT * FROM leave_types ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function createRequest(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, leave_type_id, start_date, end_date, reason, status, created_at) 
                VALUES (:employee_id, :leave_type_id, :start_date, :end_date, :reason, 'pending', NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':leave_type_id', $data['leave_type_id'], PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':reason', $data['reason']);
        
        return $this->db->execute();
    }

    public function updateRequest(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :employee_id, leave_type_id = :leave_type_id, 
                    start_date = :start_date, end_date = :end_date, reason = :reason 
                WHERE id = :id AND status = 'pending'"; // التعديل للمسودات فقط
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':leave_type_id', $data['leave_type_id'], PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

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

    public function deleteRequest(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND status = 'pending'");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}