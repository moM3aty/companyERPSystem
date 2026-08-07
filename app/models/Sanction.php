<?php
// app/models/Sanction.php

class Sanction extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sanctions';
    }

    public function getAllSanctions(): array {
        $sql = "SELECT s.*, 
                       e.name as employee_name, 
                       u.name as created_by_name 
                FROM {$this->table} s 
                LEFT JOIN employees e ON s.employee_id = e.id 
                LEFT JOIN users u ON s.created_by = u.id 
                ORDER BY s.date DESC, s.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getSanctionById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getTotalDeductions(): float {
        $sql = "SELECT SUM(amount) as total FROM {$this->table} WHERE type = 'deduction'";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }

    public function getWarningsCount(): int {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE type = 'warning'";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->count : 0;
    }

    public function createSanction(array $data): bool {
        $sql = "INSERT INTO {$this->table} (employee_id, type, amount, date, reason, created_by, created_at) 
                VALUES (:employee_id, :type, :amount, :date, :reason, :created_by, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function updateSanction(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :employee_id, type = :type, amount = :amount, 
                    date = :date, reason = :reason 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':employee_id', $data['employee_id'], PDO::PARAM_INT);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteSanction(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}