<?php
// app/models/Supplier.php

class Supplier extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'suppliers';
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
            'company_id' => "INT DEFAULT 1",
            'name'       => "VARCHAR(255) NOT NULL",
            'email'      => "VARCHAR(100) DEFAULT NULL",
            'phone'      => "VARCHAR(50) DEFAULT NULL",
            'address'    => "TEXT DEFAULT NULL",
            'tax_number' => "VARCHAR(100) DEFAULT NULL",
            'balance'    => "DECIMAL(15,2) DEFAULT 0.00",
            'created_at' => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllSuppliers() {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getSupplierById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createSupplier($data) {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, phone, address, tax_number, balance) 
                VALUES (:cid, :name, :email, :phone, :address, :tax, :balance)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':tax', $data['tax_number']);
        $this->db->bind(':balance', $data['balance'] ?? 0);
        return $this->db->execute();
    }

    public function updateSupplier($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET name = :name, email = :email, phone = :phone, address = :address, tax_number = :tax, balance = :balance 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':tax', $data['tax_number']);
        $this->db->bind(':balance', $data['balance'] ?? 0);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteSupplier($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}