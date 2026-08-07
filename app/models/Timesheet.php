<?php
// المسار: app/models/Timesheet.php

class Timesheet extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'project_timesheets';
    }

    /**
     * جلب سجلات الوقت لمشروع معين
     */
    public function getTimesheetsByProject(int $projectId): array {
        $sql = "SELECT pt.*, e.name as employee_name, t.title as task_title 
                FROM {$this->table} pt 
                JOIN employees e ON pt.employee_id = e.id 
                LEFT JOIN project_tasks t ON pt.task_id = t.id 
                WHERE pt.project_id = :project_id 
                ORDER BY pt.date DESC, pt.start_time DESC";
        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // إضافة الدالة المفقودة لجلب كافة سجلات الأوقات للشركة الحالية
    public function getAllTimesheets(): array {
        $sql = "SELECT pt.*, e.name as employee_name, t.title as task_title, p.name as project_name
                FROM {$this->table} pt 
                JOIN employees e ON pt.employee_id = e.id 
                LEFT JOIN project_tasks t ON pt.task_id = t.id 
                JOIN projects p ON pt.project_id = p.id
                WHERE p.company_id = :cid
                ORDER BY pt.date DESC, pt.start_time DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * تسجيل وقت جديد (Log Time)
     */
    public function logTime(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (project_id, task_id, employee_id, date, start_time, end_time, total_hours, note, created_at) 
                VALUES 
                (:project_id, :task_id, :employee_id, :date, :start_time, :end_time, :total_hours, :note, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':project_id', $data['project_id'], PDO::PARAM_INT);
        $this->db->bind(':task_id', $data['task_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':total_hours', $data['total_hours']);
        $this->db->bind(':note', $data['note']);
        
        return $this->db->execute();
    }

    /**
     * حساب إجمالي الساعات المنقضية على مشروع معين
     */
    public function getTotalHoursForProject(int $projectId): float {
        $this->db->query("SELECT SUM(total_hours) as total FROM {$this->table} WHERE project_id = :project_id");
        $this->db->bind(':project_id', $projectId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }
}