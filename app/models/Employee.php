<?php
// المسار: app/models/Employee.php

class Employee extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employees';
    }

    public function getAllEmployees(): array {
        $sql = "SELECT e.*, d.name as department_name 
                FROM {$this->table} e 
                LEFT JOIN departments d ON e.department_id = d.id 
                ORDER BY e.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getDepartments(): array {
        $this->db->query("SELECT id, name FROM departments ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
        }
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }
}