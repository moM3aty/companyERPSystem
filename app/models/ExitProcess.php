<?php
// app/models/ExitProcess.php

class ExitProcess extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_exits';
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
            'employee_id'      => "INT NOT NULL",
            'resignation_date' => "DATE NOT NULL",
            'last_working_day' => "DATE NOT NULL",
            'reason'           => "TEXT NULL",
            'notice_period'    => "INT DEFAULT 30",
            'exit_interview'   => "TEXT NULL",
            'final_salary'     => "DECIMAL(15,2) DEFAULT 0.00",
            'leave_balance'    => "DECIMAL(10,2) DEFAULT 0.00",
            'eos_calculation'  => "DECIMAL(15,2) DEFAULT 0.00",
            'assets_returned'  => "TINYINT(1) DEFAULT 0",
            'accounts_disabled'=> "TINYINT(1) DEFAULT 0",
            'clearance_status' => "TINYINT(1) DEFAULT 0",
            'status'           => "VARCHAR(50) DEFAULT 'pending'", // pending, completed
            'created_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllExits() {
        $sql = "SELECT x.*, e.full_name as employee_name, e.employee_number 
                FROM {$this->table} x 
                JOIN employees e ON x.employee_id = e.id 
                WHERE x.company_id = :cid 
                ORDER BY x.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getExitById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createExit($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, resignation_date, last_working_day, reason, notice_period) 
                VALUES (:cid, :emp, :rdate, :lwd, :reason, :notice)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':rdate', $data['resignation_date']);
        $this->db->bind(':lwd', $data['last_working_day']);
        $this->db->bind(':reason', $data['reason']);
        $this->db->bind(':notice', $data['notice_period']);
        
        if($this->db->execute()) {
            // تحديث حالة الموظف الأساسي
            $this->db->query("UPDATE employees SET employment_status = 'Exit Process' WHERE id = :eid");
            $this->db->bind(':eid', $data['employee_id']);
            $this->db->execute();
            return true;
        }
        return false;
    }

    public function completeExit($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET exit_interview = :ei, final_salary = :fs, leave_balance = :lb, 
                    eos_calculation = :eos, assets_returned = :ar, accounts_disabled = :ad, 
                    clearance_status = :cs, status = 'completed' 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':ei', $data['exit_interview']);
        $this->db->bind(':fs', $data['final_salary']);
        $this->db->bind(':lb', $data['leave_balance']);
        $this->db->bind(':eos', $data['eos_calculation']);
        $this->db->bind(':ar', isset($data['assets_returned']) ? 1 : 0);
        $this->db->bind(':ad', isset($data['accounts_disabled']) ? 1 : 0);
        $this->db->bind(':cs', isset($data['clearance_status']) ? 1 : 0);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        if($this->db->execute()) {
            $exit = $this->getExitById($id);
            if($exit) {
                $this->db->query("UPDATE employees SET employment_status = 'Terminated', resignation_date = :rd, last_working_day = :lwd, reason_for_leaving = :rsn WHERE id = :eid");
                $this->db->bind(':rd', $exit->resignation_date);
                $this->db->bind(':lwd', $exit->last_working_day);
                $this->db->bind(':rsn', $exit->reason);
                $this->db->bind(':eid', $exit->employee_id);
                $this->db->execute();
            }
            return true;
        }
        return false;
    }
}