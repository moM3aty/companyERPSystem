<?php
// app/models/Attendance.php

class Attendance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'attendance';
    }

    public function getDailyAttendance(string $date): array {
        $sql = "SELECT a.*, e.name as employee_name, e.position 
                FROM {$this->table} a 
                LEFT JOIN employees e ON a.employee_id = e.id 
                WHERE a.date = :date 
                ORDER BY a.created_at DESC, e.name ASC";
                
        $this->db->query($sql);
        $this->db->bind(':date', $date);
        return $this->db->resultSet();
    }

    public function getAttendanceById(int $id): ?object {
        $sql = "SELECT a.*, e.name as employee_name 
                FROM {$this->table} a 
                LEFT JOIN employees e ON a.employee_id = e.id 
                WHERE a.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function checkExists(int $empId, string $date, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE employee_id = :emp_id AND date = :date";
        if ($excludeId) $sql .= " AND id != :exclude_id";
        
        $this->db->query($sql);
        $this->db->bind(':emp_id', $empId, PDO::PARAM_INT);
        $this->db->bind(':date', $date);
        if ($excludeId) $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function createAttendance(array $data): bool {
        // إذا كان موجوداً، نمنع الإضافة ونطلب منه التعديل بدلاً من ذلك
        if ($this->checkExists($data['employee_id'], $data['date'])) {
            return false;
        }

        $sql = "INSERT INTO {$this->table} (employee_id, date, check_in, check_out, status, notes, created_at) 
                VALUES (:employee_id, :date, :check_in, :check_out, :status, :notes, NOW())";

        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':check_in', empty($data['check_in']) ? null : $data['check_in']);
        $this->db->bind(':check_out', empty($data['check_out']) ? null : $data['check_out']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        
        return $this->db->execute();
    }

    public function updateAttendance(int $id, array $data): bool {
        if ($this->checkExists($data['employee_id'], $data['date'], $id)) {
            return false;
        }

        $sql = "UPDATE {$this->table} 
                SET employee_id = :employee_id, date = :date, check_in = :check_in, 
                    check_out = :check_out, status = :status, notes = :notes 
                WHERE id = :id";

        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':check_in', empty($data['check_in']) ? null : $data['check_in']);
        $this->db->bind(':check_out', empty($data['check_out']) ? null : $data['check_out']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteAttendance(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}