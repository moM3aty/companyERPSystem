<?php
// app/models/Employee.php

class Employee extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employees';
    }

    public function getAllEmployees(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function findById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        if (empty($email)) return false;
        
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excludeId !== null) {
            $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function createEmployee(array $data): bool {
        $sql = "INSERT INTO {$this->table} (name, email, phone, position, salary, hire_date, created_at) 
                VALUES (:name, :email, :phone, :position, :salary, :hire_date, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':hire_date', $data['hire_date']);
        
        return $this->db->execute();
    }

    public function updateEmployee(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, email = :email, phone = :phone, 
                    position = :position, salary = :salary, hire_date = :hire_date 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':position', $data['position']);
        $this->db->bind(':salary', $data['salary']);
        $this->db->bind(':hire_date', $data['hire_date']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteEmployee(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}