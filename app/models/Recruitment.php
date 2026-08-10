<?php
// app/models/Recruitment.php

class Recruitment extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'candidates';
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
            'company_id'       => "INT DEFAULT 1",
            'name'             => "VARCHAR(150) NOT NULL",
            'email'            => "VARCHAR(100) DEFAULT NULL",
            'phone'            => "VARCHAR(50) DEFAULT NULL",
            'position_applied' => "VARCHAR(150) NOT NULL",
            'nationality'      => "VARCHAR(100) DEFAULT NULL",
            'expected_salary'  => "DECIMAL(10,2) DEFAULT 0.00",
            'source'           => "VARCHAR(100) DEFAULT 'direct'",
            'status'           => "VARCHAR(50) DEFAULT 'applied'", // applied, screening, interview, offered, hired, rejected
            'interview_date'   => "DATETIME DEFAULT NULL",
            'interview_score'  => "INT DEFAULT 0",
            'notes'            => "TEXT DEFAULT NULL",
            'created_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllCandidates() {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY created_at DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getCandidateById(int $id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createCandidate(array $data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, name, email, phone, position_applied, nationality, expected_salary, source, status, interview_date, notes) 
                VALUES (:cid, :name, :email, :phone, :pos, :nat, :sal, :source, :status, :idate, :notes)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':pos', $data['position_applied']);
        $this->db->bind(':nat', $data['nationality'] ?? null);
        $this->db->bind(':sal', $data['expected_salary'] ?? 0);
        $this->db->bind(':source', $data['source'] ?? 'direct');
        $this->db->bind(':status', $data['status'] ?? 'applied');
        $this->db->bind(':idate', !empty($data['interview_date']) ? $data['interview_date'] : null);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        return $this->db->execute();
    }

    public function updateStatus(int $id, string $status, int $score = 0) {
        $this->db->query("UPDATE {$this->table} SET status = :status, interview_score = :score WHERE id = :id AND company_id = :cid");
        $this->db->bind(':status', $status);
        $this->db->bind(':score', $score);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteCandidate(int $id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}