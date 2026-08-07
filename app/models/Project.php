<?php
// app/models/Project.php

class Project extends Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'projects';
    }

    public function getAllProjects(): array {
        $sql = "SELECT p.*, c.name as customer_name 
                FROM {$this->table} p 
                LEFT JOIN customers c ON p.customer_id = c.id 
                WHERE p.company_id = :cid
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getProjectById(int $id): ?object {
        $sql = "SELECT p.*, c.name as customer_name 
                FROM {$this->table} p 
                LEFT JOIN customers c ON p.customer_id = c.id 
                WHERE p.id = :id AND p.company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createProject(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, code, customer_id, start_date, end_date, budget, status, description, created_at) 
                VALUES (:cid, :name, :code, :customer_id, :start_date, :end_date, :budget, :status, :description, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':customer_id', $data['customer_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':description', $data['description']);
        return $this->db->execute();
    }

    public function updateProject(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, code = :code, customer_id = :customer_id, start_date = :start_date, end_date = :end_date, budget = :budget, status = :status, description = :description 
                WHERE id = :id AND company_id = :cid";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':customer_id', $data['customer_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function deleteProject(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }

    // ==========================================
    // إدارة المهام التشغيلية للمشروع (Tasks)
    // ==========================================

    /**
     * جلب جميع مهام مشروع معين مع اسم الموظف المسؤول
     */
    public function getProjectTasks(int $projectId): array {
        $sql = "SELECT t.*, e.name as assigned_to_name 
                FROM project_tasks t 
                LEFT JOIN employees e ON t.assigned_to = e.id 
                WHERE t.project_id = :project_id 
                ORDER BY t.start_date ASC";
        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * إنشاء مهمة جديدة تابعة لمشروع
     */
    public function createTask(array $data): bool {
        $sql = "INSERT INTO project_tasks (project_id, title, assigned_to, start_date, due_date, progress, created_at) 
                VALUES (:project_id, :title, :assigned_to, :start_date, :due_date, 0, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':project_id', $data['project_id'] ?? $data[0] ?? 0, PDO::PARAM_INT); // دعم لتمرير $projectId
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':assigned_to', !empty($data['assigned_to']) ? $data['assigned_to'] : null, PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':due_date', $data['due_date']);
        
        return $this->db->execute();
    }

    /**
     * تحديث نسبة إنجاز المهمة
     */
    public function updateTaskProgress(int $taskId, int $progress): bool {
        $sql = "UPDATE project_tasks SET progress = :progress WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':progress', $progress, PDO::PARAM_INT);
        $this->db->bind(':id', $taskId, PDO::PARAM_INT);
        return $this->db->execute();
    }
}