<?php
// app/models/Sanction.php

class Sanction extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sanctions';
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
            'type'        => "VARCHAR(50) NOT NULL", // warning, deduction
            'amount'      => "DECIMAL(10,2) DEFAULT 0.00",
            'date'        => "DATE NOT NULL",
            'reason'      => "TEXT NOT NULL",
            'created_by'  => "INT NOT NULL",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllSanctions() {
        $sql = "SELECT s.*, e.name as employee_name, u.name as admin_name 
                FROM {$this->table} s 
                JOIN employees e ON s.employee_id = e.id 
                LEFT JOIN users u ON s.created_by = u.id 
                WHERE s.company_id = :cid 
                ORDER BY s.date DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createSanction(array $data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, type, amount, date, reason, created_by) 
                VALUES (:cid, :emp, :type, :amt, :date, :reason, :user)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':amt', $data['amount'] ?? 0);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':user', Session::getUserId());
        
        return $this->db->execute();
    }

    public function deleteSanction(int $id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}