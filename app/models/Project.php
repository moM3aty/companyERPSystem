<?php
// app/models/Project.php
class Project extends Model {
    public function __construct() {
        parent::__construct();
        $this->table = 'projects';
    }
    public function getAllProjects(): array {
        $sql = "SELECT p.*, c.name as customer_name, e.name as manager_name
                FROM {$this->table} p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN employees e ON p.project_manager = e.id
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    public function getProjectById(int $id): ?object {
        $sql = "SELECT p.*, c.name as customer_name, e.name as manager_name
                FROM {$this->table} p
                LEFT JOIN customers c ON p.customer_id = c.id
                LEFT JOIN employees e ON p.project_manager = e.id
                WHERE p.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    public function createProject(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (name, code, customer_id, description, start_date, end_date, status, budget, project_manager, created_at) 
                VALUES 
                (:name, :code, :customer_id, :description, :start_date, :end_date, :status, :budget, :project_manager, NOW())";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':customer_id', $data['customer_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date'] ?: null);
        $this->db->bind(':end_date', $data['end_date'] ?: null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':project_manager', $data['project_manager'] ?: null, PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function updateProject(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, code = :code, customer_id = :customer_id, description = :description, 
                    start_date = :start_date, end_date = :end_date, status = :status, budget = :budget, 
                    project_manager = :project_manager 
                WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':customer_id', $data['customer_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':start_date', $data['start_date'] ?: null);
        $this->db->bind(':end_date', $data['end_date'] ?: null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':budget', $data['budget']);
        $this->db->bind(':project_manager', $data['project_manager'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function deleteProject(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    public function getProjectTasks(int $projectId): array {
        $sql = "SELECT pt.*, e.name as assigned_to_name
                FROM project_tasks pt
                LEFT JOIN employees e ON pt.assigned_to = e.id
                WHERE pt.project_id = :project_id
                ORDER BY pt.start_date ASC";
        $this->db->query($sql);
        $this->db->bind(':project_id', $projectId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
        public function createTask(array $data): bool {
        $sql = "INSERT INTO project_tasks (project_id, title, start_date, due_date, progress, assigned_to, created_at) 
                VALUES (:project_id, :title, :start_date, :due_date, :progress, :assigned_to, NOW())";
        $this->db->query($sql);
        $this->db->bind(':project_id', $data['project_id'], PDO::PARAM_INT);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':progress', $data['progress'], PDO::PARAM_INT);
        $this->db->bind(':assigned_to', $data['assigned_to'] ?: null, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function updateTaskProgress(int $taskId, int $progress): bool {
        $sql = "UPDATE project_tasks SET progress = :progress WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':progress', $progress, PDO::PARAM_INT);
        $this->db->bind(':id', $taskId, PDO::PARAM_INT);
        return $this->db->execute();
    }
}