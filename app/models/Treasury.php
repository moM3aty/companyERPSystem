<?php
// app/models/Treasury.php

class Treasury extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'treasuries';
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
            'name'            => "VARCHAR(150) NOT NULL",
            'type'            => "VARCHAR(50) DEFAULT 'Cash'", // Cash, Bank, Petty Cash
            'account_number'  => "VARCHAR(100) NULL", // IBAN or Account No for Banks
            'currency'        => "VARCHAR(10) DEFAULT 'SAR'",
            'opening_balance' => "DECIMAL(15,2) DEFAULT 0.00",
            'current_balance' => "DECIMAL(15,2) DEFAULT 0.00",
            'chart_account_id'=> "INT NULL", // Link to Chart of Accounts
            'is_active'       => "TINYINT(1) DEFAULT 1",
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

    public function getAllTreasuries() {
        $sql = "SELECT t.*, a.account_name as linked_account 
                FROM {$this->table} t 
                LEFT JOIN accounting_accounts a ON t.chart_account_id = a.id 
                WHERE t.company_id = :cid ORDER BY t.id ASC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getTreasuryById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createTreasury($data) {
        $sql = "INSERT INTO {$this->table} (company_id, name, type, account_number, currency, opening_balance, current_balance, chart_account_id) 
                VALUES (:cid, :name, :type, :acc_no, :curr, :open_bal, :curr_bal, :chart_id)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':acc_no', $data['account_number']);
        $this->db->bind(':curr', $data['currency']);
        $this->db->bind(':open_bal', $data['opening_balance']);
        $this->db->bind(':curr_bal', $data['opening_balance']); // Start with opening balance
        $this->db->bind(':chart_id', !empty($data['chart_account_id']) ? $data['chart_account_id'] : null);
        return $this->db->execute();
    }

    public function updateTreasury($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                name = :name, type = :type, account_number = :acc_no, currency = :curr, chart_account_id = :chart_id 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':acc_no', $data['account_number']);
        $this->db->bind(':curr', $data['currency']);
        $this->db->bind(':chart_id', !empty($data['chart_account_id']) ? $data['chart_account_id'] : null);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function updateBalance($id, $amount, $type = 'add') {
        $operator = $type === 'add' ? '+' : '-';
        $sql = "UPDATE {$this->table} SET current_balance = current_balance {$operator} :amount WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':amount', $amount);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}