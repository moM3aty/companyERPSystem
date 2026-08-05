<?php
// app/models/User.php

class User extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
    }

    public function getAllUsers(): array {
        $sql = "SELECT id, name, email, role, phone, created_at 
                FROM {$this->table} 
                ORDER BY created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createUser(array $data): bool {
        // تشفير كلمة المرور بشكل آمن جداً
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO {$this->table} (name, email, password, role, phone, created_at) 
                VALUES (:name, :email, :password, :role, :phone, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        
        return $this->db->execute();
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