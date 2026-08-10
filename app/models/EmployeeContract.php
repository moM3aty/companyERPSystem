<?php
// app/models/EmployeeContract.php

class EmployeeContract extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_contracts';
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
            'employee_id'  => "INT NOT NULL",
            'start_date'   => "DATE NOT NULL",
            'end_date'     => "DATE DEFAULT NULL",
            'basic_salary' => "DECIMAL(10,2) NOT NULL DEFAULT 0.00",
            'allowances'   => "DECIMAL(10,2) DEFAULT 0.00",
            'status'       => "VARCHAR(50) DEFAULT 'active'", // active, expired, terminated
            'notes'        => "TEXT DEFAULT NULL",
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

    public function getAllContracts() {
        $sql = "SELECT c.*, e.name as employee_name, e.employee_number 
                FROM {$this->table} c 
                JOIN employees e ON c.employee_id = e.id 
                WHERE c.company_id = :cid 
                ORDER BY c.end_date ASC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getContractById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createContract($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, start_date, end_date, basic_salary, allowances, status, notes) 
                VALUES (:cid, :emp, :sdate, :edate, :bsalary, :allowances, :status, :notes)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':sdate', $data['start_date']);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':bsalary', $data['basic_salary']);
        $this->db->bind(':allowances', $data['allowances']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        
        if($this->db->execute()) {
            // Update salary in main employee profile
            $this->db->query("UPDATE employees SET basic_salary = :sal WHERE id = :eid");
            $this->db->bind(':sal', $data['basic_salary']);
            $this->db->bind(':eid', $data['employee_id']);
            $this->db->execute();
            return true;
        }
        return false;
    }

    public function updateContract($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, start_date = :sdate, end_date = :edate, 
                    basic_salary = :bsalary, allowances = :allowances, status = :status, notes = :notes 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':sdate', $data['start_date']);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':bsalary', $data['basic_salary']);
        $this->db->bind(':allowances', $data['allowances']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        if($this->db->execute()) {
            // Update salary in main employee profile if contract is active
            if ($data['status'] === 'active') {
                $this->db->query("UPDATE employees SET basic_salary = :sal WHERE id = :eid");
                $this->db->bind(':sal', $data['basic_salary']);
                $this->db->bind(':eid', $data['employee_id']);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }

    public function deleteContract($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}