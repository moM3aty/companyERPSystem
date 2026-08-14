<?php
// app/models/Account.php

class Account extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'accounting_accounts';
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
            'account_code' => "VARCHAR(50) NOT NULL",
            'account_name' => "VARCHAR(150) NOT NULL",
            'account_type' => "VARCHAR(50) NOT NULL", // Asset, Liability, Equity, Revenue, Expense
            'parent_id'    => "INT DEFAULT NULL",
            'balance'      => "DECIMAL(15,2) DEFAULT 0.00",
            'is_active'    => "TINYINT(1) DEFAULT 1",
            'description'  => "TEXT NULL",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllAccounts() {
        $this->db->query("SELECT a.*, p.account_name as parent_name 
                          FROM {$this->table} a 
                          LEFT JOIN {$this->table} p ON a.parent_id = p.id 
                          WHERE a.company_id = :cid ORDER BY a.account_code ASC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function createAccount($data) {
        $sql = "INSERT INTO {$this->table} (company_id, account_code, account_name, account_type, parent_id, description) 
                VALUES (:cid, :code, :name, :type, :parent, :desc)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':code', $data['account_code']);
        $this->db->bind(':name', $data['account_name']);
        $this->db->bind(':type', $data['account_type']);
        $this->db->bind(':parent', !empty($data['parent_id']) ? $data['parent_id'] : null);
        $this->db->bind(':desc', $data['description']);
        return $this->db->execute();
    }

    public function deleteAccount($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}