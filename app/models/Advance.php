<?php
// app/models/Advance.php

class Advance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_advances';
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
            'company_id'      => "INT DEFAULT 1",
            'employee_id'     => "INT NOT NULL",
            'amount'          => "DECIMAL(10,2) NOT NULL DEFAULT 0.00",
            'date'            => "DATE NOT NULL",
            'deduction_month' => "INT(2) NOT NULL",
            'deduction_year'  => "INT(4) NOT NULL",
            'reason'          => "TEXT DEFAULT NULL",
            'status'          => "VARCHAR(50) DEFAULT 'pending'", // pending, approved, rejected, deducted
            'approved_by'     => "INT DEFAULT NULL",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllAdvances() {
        $sql = "SELECT a.*, e.name as employee_name, u.name as approver_name 
                FROM {$this->table} a 
                JOIN employees e ON a.employee_id = e.id 
                LEFT JOIN users u ON a.approved_by = u.id 
                WHERE a.company_id = :cid 
                ORDER BY a.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createAdvance($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, amount, date, deduction_month, deduction_year, reason, status) 
                VALUES (:cid, :emp, :amount, :date, :dmonth, :dyear, :reason, 'pending')";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':dmonth', $data['deduction_month']);
        $this->db->bind(':dyear', $data['deduction_year']);
        $this->db->bind(':reason', $data['reason'] ?? null);
        return $this->db->execute();
    }

    public function updateStatus($id, $status, $userId) {
        $this->db->query("UPDATE {$this->table} SET status = :status, approved_by = :uid WHERE id = :id AND company_id = :cid");
        $this->db->bind(':status', $status);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteAdvance($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}