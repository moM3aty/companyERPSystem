<?php
// app/models/Treasury.php

class Treasury extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'treasuries';$this->autoUpgradeTable();
    }

    /* STREAMING_CHUNK: Auto-upgrading tables... */
    private function autoUpgradeTable() {
        // 1. جدول الخزن والبنوك
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `treasuries` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id' => "INT DEFAULT 1",
            'name'       => "VARCHAR(255) NOT NULL",
            'type'       => "VARCHAR(50) DEFAULT 'cash'", 
            'balance'    => "DECIMAL(15,2) DEFAULT 0.00",
            'created_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col =>$def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `treasuries` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `treasuries` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // 2. جدول حركات الخزينة (إيداع / سحب)
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `treasury_transactions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $transColumns = [
            'company_id'       => "INT DEFAULT 1",
            'treasury_id'      => "INT NOT NULL",
            'type'             => "VARCHAR(50) NOT NULL DEFAULT 'deposit'",
            'amount'           => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'transaction_date' => "DATE NOT NULL",
            'reference'        => "VARCHAR(100) DEFAULT NULL",
            'notes'            => "TEXT DEFAULT NULL",
            'created_by'       => "INT DEFAULT 0",
            'created_at'       => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($transColumns as $col =>$def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `treasury_transactions` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {$this->db->query("ALTER TABLE `treasury_transactions` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    /* STREAMING_CHUNK: Fetching operations... */
    public function getAllTreasuries(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id ASC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getTreasuryById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createTreasury(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, type, balance) VALUES (:cid, :name, :type, :balance)";
        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name',$data['name']);
        $this->db->bind(':type',$data['type'] ?? 'cash');
        $this->db->bind(':balance',$data['balance'] ?? 0);
        return $this->db->execute();
    }

    public function updateTreasury(int $id, array$data): bool {
        $sql = "UPDATE {$this->table} SET name = :name, type = :type WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);$this->db->bind(':name', $data['name']);$this->db->bind(':type', $data['type'] ?? 'cash');$this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteTreasury(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    /* STREAMING_CHUNK: Transactions... */
    public function getTransactions(?int $treasuryId = null): array {$sql = "SELECT t.*, tr.name as treasury_name, u.name as creator_name 
                FROM treasury_transactions t 
                LEFT JOIN treasuries tr ON t.treasury_id = tr.id 
                LEFT JOIN users u ON t.created_by = u.id 
                WHERE t.company_id = :cid ";
        
        if ($treasuryId) {$sql .= " AND t.treasury_id = :tid ";
        }
        
        $sql .= " ORDER BY t.transaction_date DESC, t.id DESC";

        $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
        if ($treasuryId) {
            $this->db->bind(':tid',$treasuryId);
        }
        return $this->db->resultSet();
    }

    public function createTransaction(array $data): bool {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO treasury_transactions (company_id, treasury_id, type, amount, transaction_date, reference, notes, created_by) 
                    VALUES (:cid, :tid, :type, :amt, :tdate, :ref, :notes, :user)";
            $this->db->query($sql);$this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':tid',$data['treasury_id']);
            $this->db->bind(':type',$data['type']);
            $this->db->bind(':amt',$data['amount']);
            $this->db->bind(':tdate',$data['transaction_date']);
            $this->db->bind(':ref',$data['reference'] ?? null);
            $this->db->bind(':notes',$data['notes'] ?? null);
            $this->db->bind(':user', Session::getUserId());$this->db->execute();

            // تحديث الرصيد (إيداع يزيد، سحب ينقص)
            if ($data['type'] === 'deposit') {$sqlBal = "UPDATE treasuries SET balance = balance + :amt WHERE id = :tid";
            } else {
                $sqlBal = "UPDATE treasuries SET balance = balance - :amt WHERE id = :tid";
            }
            $this->db->query($sqlBal);$this->db->bind(':amt', $data['amount']);$this->db->bind(':tid', $data['treasury_id']);$this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {$this->db->rollBack();
            return false;
        }
    }
}