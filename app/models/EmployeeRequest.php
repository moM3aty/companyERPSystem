<?php
// app/models/EmployeeRequest.php

class EmployeeRequest extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'hr_employee_requests';$this->autoUpgradeTable();
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
            'employee_id'  => "INT NOT NULL",
            'request_type' => "VARCHAR(100) NOT NULL", // Salary Certificate, Expense Reimbursement, Overtime...
            'details'      => "TEXT NOT NULL",
            'status'       => "VARCHAR(50) DEFAULT 'pending'", // pending, approved, rejected
            'hr_notes'     => "TEXT NULL",
            'action_by'    => "INT NULL",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col =>$def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllRequests() {
        $sql = "SELECT r.*, e.name as employee_name, e.employee_number, u.name as action_by_name 
                FROM {$this->table} r 
                JOIN employees e ON r.employee_id = e.id 
                LEFT JOIN users u ON r.action_by = u.id 
                WHERE r.company_id = :cid 
                ORDER BY r.created_at DESC";
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getRequestById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createRequest($data) {
        $sql = "INSERT INTO {$this->table} (company_id, employee_id, request_type, details, status) 
                VALUES (:cid, :emp, :type, :details, 'pending')";
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp',$data['employee_id']);
        $this->db->bind(':type',$data['request_type']);
        $this->db->bind(':details',$data['details']);
        return $this->db->execute();
    }

    public function updateRequest($id,$data) {
        $sql = "UPDATE {$this->table} SET status = :status, hr_notes = :notes, action_by = :user WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':status',$data['status']);
        $this->db->bind(':notes',$data['hr_notes']);
        $this->db->bind(':user', Session::getUserId());$this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteRequest($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}