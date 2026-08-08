<?php
// app/models/User.php

class User extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'users';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columnsToAdd = [
            'company_id' => "INT DEFAULT 1",
            'name'       => "VARCHAR(255) NOT NULL",
            'email'      => "VARCHAR(255) NOT NULL",
            'password'   => "VARCHAR(255) NOT NULL",
            'role'       => "VARCHAR(50) DEFAULT 'viewer'",
            'created_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllUsers(): array {
        $this->db->query("SELECT id, name, email, role, created_at FROM {$this->table} WHERE company_id = :cid OR company_id IS NULL ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getUserById(int $id): ?object {
        $this->db->query("SELECT id, name, email, role FROM {$this->table} WHERE id = :id AND (company_id = :cid OR company_id IS NULL) LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE email = :email";
        if ($excludeId) {
            $sql .= " AND id != :excludeId";
        }
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        if ($excludeId) {
            $this->db->bind(':excludeId', $excludeId);
        }
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function createUser(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, password, role, created_at) VALUES (:cid, :name, :email, :password, :role, NOW())";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':role', $data['role']);
        return $this->db->execute();
    }

    public function updateUser(int $id, array $data): bool {
        if (!empty($data['password'])) {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role, password = :password WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        } else {
            $sql = "UPDATE {$this->table} SET name = :name, email = :email, role = :role WHERE id = :id";
            $this->db->query($sql);
        }
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function deleteUser(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}