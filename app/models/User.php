<?php
// app/models/User.php

class User extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'users';$this->autoUpgradeTable();
    }

    /* STREAMING_CHUNK: Upgrade Table for Multi-Tenancy */
    private function autoUpgradeTable() {
        try {
            $this->db->query("SHOW COLUMNS FROM `users` LIKE 'company_id'");
            if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `users` ADD `company_id` INT DEFAULT 1");
                $this->db->execute();
            }
            
            $this->db->query("SHOW COLUMNS FROM `users` LIKE 'role'");
            if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `users` ADD `role` VARCHAR(50) DEFAULT 'user'");
                $this->db->execute();
            }
        } catch (Exception $e) {}
    }

    /* STREAMING_CHUNK: Authentication Methods */
    // 🟢 دالة البحث عن المستخدم بالبريد الإلكتروني 🟢
    public function findUserByEmail(string $email) {
        $this->db->query("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $this->db->bind(':email',$email);
        return $this->db->single();
    }

    // 🟢 دالة التحقق من تسجيل الدخول 🟢
    public function login(string $email, string $password) {$row = $this->findUserByEmail($email);
        if ($row) {
            $hashedPassword =$row->password;
            if (password_verify($password,$hashedPassword)) {
                return $row;
            }
        }
        return false;
    }

    /* STREAMING_CHUNK: CRUD Operations */
    public function getAllUsers(): array {
        $this->db->query("SELECT id, name, email, role, created_at FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getUserById(int $id): ?object {
        $this->db->query("SELECT id, name, email, role FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {$sql = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($excludeId)$sql .= " AND id != :excludeId";
        
        $this->db->query($sql);
        $this->db->bind(':email',$email);
        if ($excludeId)$this->db->bind(':excludeId', $excludeId);$this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function createUser(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, role, password) VALUES (:cid, :name, :email, :role, :password)";
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name',$data['name']);
        $this->db->bind(':email',$data['email']);
        $this->db->bind(':role',$data['role']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        return $this->db->execute();
    }

    // 🟢 دالة خاصة للـ SuperAdmin لإنشاء مدير لشركة جديدة 🟢
    public function createUserForTenant(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, role, password) VALUES (:cid, :name, :email, :role, :password)";
        $this->db->query($sql);
        $this->db->bind(':cid',$data['company_id']);
        $this->db->bind(':name',$data['name']);
        $this->db->bind(':email',$data['email']);
        $this->db->bind(':role',$data['role']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        return $this->db->execute();
    }

    public function updateUser(int $id, array$data): bool {
        if (!empty($data['password'])) {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role, password = :password WHERE id = :id AND company_id = :cid";
            $this->db->query($sql);
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role WHERE id = :id AND company_id = :cid";
            $this->db->query($sql);
        }
        
        $this->db->bind(':name', $data['name']);$this->db->bind(':email', $data['email']);$this->db->bind(':role', $data['role']);$this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        return $this->db->execute();
    }

    public function deleteUser(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}