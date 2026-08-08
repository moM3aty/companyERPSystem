<?php
// app/models/Project.php

class Project extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'projects';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // جدول المشاريع (الإنشاء إذا لم يكن موجوداً)
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `projects` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        // 🟢 إضافة الأعمدة المفقودة تلقائياً (مثل created_by و company_id) 🟢
        $columnsToAdd = [
            'company_id'  => "INT DEFAULT 1",
            'code'        => "VARCHAR(50) DEFAULT NULL",
            'description' => "TEXT DEFAULT NULL",
            'start_date'  => "DATE DEFAULT NULL",
            'end_date'    => "DATE DEFAULT NULL",
            'budget'      => "DECIMAL(15,2) DEFAULT 0.00",
            'status'      => "VARCHAR(50) DEFAULT 'active'",
            'created_by'  => "INT NOT NULL DEFAULT 0",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `projects` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `projects` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // جدول المهام
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `project_tasks` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `project_id` int(11) NOT NULL,
                `title` varchar(255) NOT NULL,
                `assigned_to` int(11) DEFAULT NULL,
                `start_date` date DEFAULT NULL,
                `due_date` date DEFAULT NULL,
                `progress` int(11) DEFAULT 0,
                `created_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getAllProjects(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getProjectById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createProject(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, code, description, start_date, end_date, budget, status, created_by) 
                VALUES (:cid, :name, :code, :desc, :sdate, :edate, :budget, :status, :user)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code'] ?? null);
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':sdate', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':budget', $data['budget'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':user', Session::getUserId());
        return $this->db->execute();
    }

    public function updateProject(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, code = :code, description = :desc, start_date = :sdate, 
                    end_date = :edate, budget = :budget, status = :status 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code'] ?? null);
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':sdate', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':budget', $data['budget'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteProject(int $id): bool {
        try {
            $this->db->query("DELETE FROM project_tasks WHERE project_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            $this->db->query("DELETE FROM timesheets WHERE project_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
        } catch (Exception $e) {}

        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function getTasks(int $projectId): array {
        $this->db->query("SELECT t.*, e.name as assigned_to_name 
                          FROM project_tasks t 
                          LEFT JOIN employees e ON t.assigned_to = e.id 
                          WHERE t.project_id = :pid ORDER BY t.start_date ASC");
        $this->db->bind(':pid', $projectId);
        return $this->db->resultSet();
    }

    public function addTask(array $data): bool {
        $sql = "INSERT INTO project_tasks (project_id, title, assigned_to, start_date, due_date, progress) 
                VALUES (:pid, :title, :assigned, :sdate, :ddate, 0)";
        $this->db->query($sql);
        $this->db->bind(':pid', $data['project_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':assigned', !empty($data['assigned_to']) ? $data['assigned_to'] : null);
        $this->db->bind(':sdate', !empty($data['start_date']) ? $data['start_date'] : date('Y-m-d'));
        $this->db->bind(':ddate', !empty($data['due_date']) ? $data['due_date'] : date('Y-m-d'));
        return $this->db->execute();
    }

    public function updateTaskProgress(int $taskId, int $progress): bool {
        $this->db->query("UPDATE project_tasks SET progress = :prog WHERE id = :id");
        $this->db->bind(':prog', $progress);
        $this->db->bind(':id', $taskId);
        return $this->db->execute();
    }
}