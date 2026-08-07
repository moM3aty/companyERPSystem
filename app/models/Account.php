<?php
// app/models/Account.php

class Account extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'chart_of_accounts';
    }

    public function getChartOfAccounts(): array {
        $sql = "SELECT a.*, p.name as parent_name 
                FROM {$this->table} a 
                LEFT JOIN {$this->table} p ON a.parent_id = p.id 
                ORDER BY a.type, a.code ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getParentAccounts(): array {
        $sql = "SELECT id, code, name, type FROM {$this->table} ORDER BY code ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function findById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function codeExists(string $code, ?int $excludeId = null): bool {
        if (empty($code)) return false;
        $sql = "SELECT id FROM {$this->table} WHERE code = :code";
        if ($excludeId) $sql .= " AND id != :exclude_id";
        
        $this->db->query($sql);
        $this->db->bind(':code', $code);
        if ($excludeId) $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function createAccount(array $data): bool {
        $sql = "INSERT INTO {$this->table} (code, name, type, parent_id, balance, is_active, created_at) 
                VALUES (:code, :name, :type, :parent_id, :balance, 1, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':parent_id', $data['parent_id'], PDO::PARAM_INT);
        $this->db->bind(':balance', $data['balance']);
        
        return $this->db->execute();
    }

    public function updateAccount(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET code = :code, name = :name, type = :type, parent_id = :parent_id, balance = :balance 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':parent_id', $data['parent_id'], PDO::PARAM_INT);
        $this->db->bind(':balance', $data['balance']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteAccount(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}