<?php
// app/models/Onboarding.php

class Onboarding extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'employee_onboarding';
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
            'contract_signed'  => "TINYINT(1) DEFAULT 0",
            'id_received'      => "TINYINT(1) DEFAULT 0",
            'bank_details'     => "TINYINT(1) DEFAULT 0",
            'email_created'    => "TINYINT(1) DEFAULT 0",
            'equipment_issued' => "TINYINT(1) DEFAULT 0",
            'access_card'      => "TINYINT(1) DEFAULT 0",
            'system_accounts'  => "TINYINT(1) DEFAULT 0",
            'orientation'      => "TINYINT(1) DEFAULT 0",
            'safety_training'  => "TINYINT(1) DEFAULT 0",
            'manager_assigned' => "TINYINT(1) DEFAULT 0",
            'status'           => "VARCHAR(50) DEFAULT 'pending'",
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

    public function getAllOnboarding() {
        $sql = "SELECT o.*, e.full_name as employee_name, e.position 
                FROM {$this->table} o 
                JOIN employees e ON o.employee_id = e.id 
                WHERE o.company_id = :cid 
                ORDER BY o.status DESC, o.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getOnboardingById($id) {
        $this->db->query("SELECT o.*, e.full_name as employee_name FROM {$this->table} o JOIN employees e ON o.employee_id = e.id WHERE o.id = :id AND o.company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createOnboarding($employeeId) {
        $this->db->query("SELECT id FROM {$this->table} WHERE employee_id = :emp");
        $this->db->bind(':emp', $employeeId);
        $this->db->execute();
        if ($this->db->rowCount() > 0) return true;

        $sql = "INSERT INTO {$this->table} (company_id, employee_id) VALUES (:cid, :emp)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $employeeId);
        return $this->db->execute();
    }

    public function updateOnboarding($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET contract_signed = :c, id_received = :idr, bank_details = :b, 
                    email_created = :e, equipment_issued = :eq, access_card = :a, 
                    system_accounts = :s, orientation = :o, safety_training = :saf, 
                    manager_assigned = :m, status = :status 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':c', isset($data['contract_signed']) ? 1 : 0);
        $this->db->bind(':idr', isset($data['id_received']) ? 1 : 0);
        $this->db->bind(':b', isset($data['bank_details']) ? 1 : 0);
        $this->db->bind(':e', isset($data['email_created']) ? 1 : 0);
        $this->db->bind(':eq', isset($data['equipment_issued']) ? 1 : 0);
        $this->db->bind(':a', isset($data['access_card']) ? 1 : 0);
        $this->db->bind(':s', isset($data['system_accounts']) ? 1 : 0);
        $this->db->bind(':o', isset($data['orientation']) ? 1 : 0);
        $this->db->bind(':saf', isset($data['safety_training']) ? 1 : 0);
        $this->db->bind(':m', isset($data['manager_assigned']) ? 1 : 0);
        
        $totalChecked = count(array_filter($data));
        $status = $totalChecked >= 10 ? 'completed' : 'pending';
        $this->db->bind(':status', $status);
        
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}