<?php
// app/models/Leave.php

class Leave extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'leaves';
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

        $columns = [
            'company_id'  => "INT DEFAULT 1",
            'employee_id' => "INT NOT NULL",
            'leave_type'  => "VARCHAR(50) DEFAULT 'annual'",
            'start_date'  => "DATE NOT NULL",
            'end_date'    => "DATE NOT NULL",
            'days'        => "INT NOT NULL DEFAULT 1",
            'reason'      => "TEXT NULL",
            'status'      => "VARCHAR(50) DEFAULT 'pending'",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllLeaves(): array {
        $sql = "SELECT l.*, u.name as employee_name 
                FROM {$this->table} l 
                LEFT JOIN users u ON l.employee_id = u.id 
                WHERE l.company_id = :cid 
                ORDER BY l.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getEmployeeLeaves(int $userId): array {
        $sql = "SELECT l.*, u.name as employee_name 
                FROM {$this->table} l 
                LEFT JOIN users u ON l.employee_id = u.id 
                WHERE l.employee_id = :uid AND l.company_id = :cid 
                ORDER BY l.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getLeaveById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createLeave(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, leave_type, start_date, end_date, days, reason, status) 
                VALUES (:cid, :eid, :type, :sdate, :edate, :days, :reason, 'pending')";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':eid', $data['employee_id']);
        $this->db->bind(':type', $data['leave_type']);
        $this->db->bind(':sdate', $data['start_date']);
        $this->db->bind(':edate', $data['end_date']);
        $this->db->bind(':days', $data['days']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }

    public function updateStatus(int $id, string $status): bool {
        $this->db->query("UPDATE {$this->table} SET status = :status WHERE id = :id AND company_id = :cid");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteLeave(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}