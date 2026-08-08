<?php
// app/models/Timesheet.php

class Timesheet extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'timesheets';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `timesheets` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) DEFAULT 1,
                `project_id` int(11) NOT NULL,
                `task_id` int(11) DEFAULT NULL,
                `employee_id` int(11) NOT NULL,
                `date` date NOT NULL,
                `start_time` time NOT NULL,
                `end_time` time NOT NULL,
                `total_hours` decimal(5,2) NOT NULL DEFAULT 0.00,
                `note` text DEFAULT NULL,
                `created_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getProjectTimesheets(int $projectId): array {
        $this->db->query("SELECT ts.*, e.name as employee_name, t.title as task_title 
                          FROM {$this->table} ts 
                          LEFT JOIN employees e ON ts.employee_id = e.id 
                          LEFT JOIN project_tasks t ON ts.task_id = t.id 
                          WHERE ts.project_id = :pid AND ts.company_id = :cid 
                          ORDER BY ts.date DESC, ts.start_time DESC");
        $this->db->bind(':pid', $projectId);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    // 🟢 إضافة دالة جديدة لجلب السجل الشامل لكل المشاريع 🟢
    public function getAllTimesheets(): array {
        $this->db->query("SELECT ts.*, e.name as employee_name, t.title as task_title, p.name as project_name 
                          FROM {$this->table} ts 
                          LEFT JOIN employees e ON ts.employee_id = e.id 
                          LEFT JOIN project_tasks t ON ts.task_id = t.id 
                          LEFT JOIN projects p ON ts.project_id = p.id
                          WHERE ts.company_id = :cid 
                          ORDER BY ts.date DESC, ts.start_time DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function logTime(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, project_id, task_id, employee_id, date, start_time, end_time, total_hours, note) 
                VALUES (:cid, :pid, :tid, :eid, :date, :start, :end, :hours, :note)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':pid', $data['project_id']);
        $this->db->bind(':tid', !empty($data['task_id']) ? $data['task_id'] : null);
        $this->db->bind(':eid', $data['employee_id']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':start', $data['start_time']);
        $this->db->bind(':end', $data['end_time']);
        $this->db->bind(':hours', $data['total_hours']);
        $this->db->bind(':note', $data['note'] ?? null);
        return $this->db->execute();
    }
}