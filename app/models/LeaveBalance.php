<?php
// app/models/LeaveBalance.php

class LeaveBalance extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_leave_balances';$this->autoUpgradeTable();
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
            'leave_type'       => "VARCHAR(100) NOT NULL", // Annual, Sick, etc.
            'entitlement'      => "DECIMAL(5,2) DEFAULT 0.00", // الرصيد السنوي المستحق
            'used'             => "DECIMAL(5,2) DEFAULT 0.00", // ما تم استخدامه
            'balance'          => "DECIMAL(5,2) DEFAULT 0.00", // الرصيد المتبقي
            'year'             => "INT(4) NOT NULL",
            'updated_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
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

    public function getEmployeeBalances($employeeId,$year) {
        $this->db->query("SELECT * FROM {$this->table} WHERE employee_id = :emp AND year = :year AND company_id = :cid");
        $this->db->bind(':emp', $employeeId);$this->db->bind(':year', $year);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    // دالة لتحديث الرصيد آلياً عند قبول الإجازة
    public function updateUsedLeave($employeeId,$leaveType, $year,$daysUsed) {
        $sql = "UPDATE {$this->table} 
                SET used = used + :days, balance = entitlement - (used + :days) 
                WHERE employee_id = :emp AND leave_type = :type AND year = :year AND company_id = :cid";
        $this->db->query($sql);$this->db->bind(':days', $daysUsed);$this->db->bind(':emp', $employeeId);$this->db->bind(':type', $leaveType);$this->db->bind(':year', $year);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}