<?php
class Leave extends Model {
    
    /**
     * تقديم طلب إجازة
     */
    public function request($employeeId, $leaveTypeId, $start, $end, $reason) {
        $this->db->query('
            INSERT INTO leave_requests 
            (employee_id, leave_type_id, start_date, end_date, reason, status)
            VALUES (:emp, :type, :start, :end, :reason, "pending")
        ');
        $this->db->bind(':emp', $employeeId, PDO::PARAM_INT);
        $this->db->bind(':type', $leaveTypeId, PDO::PARAM_INT);
        $this->db->bind(':start', $start);
        $this->db->bind(':end', $end);
        $this->db->bind(':reason', $reason);
        return $this->db->execute();
    }
    
    /**
     * الموافقة على طلب
     */
    public function approve($id, $approverUserId) {
        $this->db->query('
            UPDATE leave_requests SET 
                status = "approved",
                approved_by = :approver,
                approved_at = NOW()
            WHERE id = :id
        ');
        $this->db->bind(':approver', $approverUserId, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    /**
     * جلب طلبات موظف
     */
    public function getByEmployee($employeeId) {
        $this->db->query('
            SELECT lr.*, lt.name as leave_type_name 
            FROM leave_requests lr
            LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE employee_id = :emp
            ORDER BY id DESC
        ');
        $this->db->bind(':emp', $employeeId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}