<?php
// app/models/Followup.php

class Followup extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'followups';
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
            'company_id'   => "INT DEFAULT 1",
            'lead_id'      => "INT NOT NULL",
            'type'         => "VARCHAR(50) DEFAULT 'call'", // call, meeting, email
            'scheduled_at' => "DATETIME NOT NULL",
            'notes'        => "TEXT DEFAULT NULL",
            'status'       => "VARCHAR(50) DEFAULT 'pending'", // pending, completed, cancelled
            'created_by'   => "INT NOT NULL",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllFollowups(): array {
        $sql = "SELECT f.*, l.name as lead_name, l.company as lead_company, u.name as creator_name 
                FROM {$this->table} f 
                LEFT JOIN leads l ON f.lead_id = l.id 
                LEFT JOIN users u ON f.created_by = u.id 
                WHERE f.company_id = :cid 
                ORDER BY f.scheduled_at ASC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createFollowup(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, lead_id, type, scheduled_at, notes, status, created_by) 
                VALUES (:cid, :lead, :type, :sat, :notes, 'pending', :user)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':lead', $data['lead_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':sat', $data['scheduled_at']);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':user', Session::getUserId());
        return $this->db->execute();
    }

    public function markAsCompleted(int $id): bool {
        $this->db->query("UPDATE {$this->table} SET status = 'completed' WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteFollowup(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}