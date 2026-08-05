<?php
// app/models/Attendance.php

class Attendance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'attendance';
    }

    /**
     * جلب سجل الحضور والانصراف ليوم محدد
     */
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

    /**
     * التحقق مما إذا كان الموظف له سجل في هذا اليوم
     */
    public function checkExists(int $empId, string $date): bool {
        $this->db->query("SELECT id FROM {$this->table} WHERE employee_id = :emp_id AND date = :date");
        $this->db->bind(':emp_id', $empId);
        $this->db->bind(':date', $date);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * تسجيل أو تحديث حضور الموظف
     */
    public function recordAttendance(array $data): bool {
        // التحقق من وجود السجل مسبقاً لهذا اليوم
        if ($this->checkExists($data['employee_id'], $data['date'])) {
            // تحديث السجل
            $sql = "UPDATE {$this->table} 
                    SET check_in = :check_in, check_out = :check_out, status = :status, notes = :notes 
                    WHERE employee_id = :employee_id AND date = :date";
        } else {
            // إدخال جديد
            $sql = "INSERT INTO {$this->table} (employee_id, date, check_in, check_out, status, notes, created_at) 
                    VALUES (:employee_id, :date, :check_in, :check_out, :status, :notes, NOW())";
        }

        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':check_in', empty($data['check_in']) ? null : $data['check_in']);
        $this->db->bind(':check_out', empty($data['check_out']) ? null : $data['check_out']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        
        return $this->db->execute();
    }
}