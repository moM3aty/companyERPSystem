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
            'company_id'      => "INT DEFAULT 1",
            'company_name'    => "VARCHAR(255) NOT NULL",
            'contact_person'  => "VARCHAR(255) NULL",
            'phone'           => "VARCHAR(50) NULL",
            'email'           => "VARCHAR(100) NULL",
            'address'         => "TEXT NULL",
            'tax_number'      => "VARCHAR(100) NULL",
            'current_balance' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'           => "TEXT NULL",
            'created_by'      => "INT NOT NULL",
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

    public function getAllSuppliers() {
        try {
            $sql = "SELECT s.*, u.name as user_name 
                    FROM {$this->table} s 
                    LEFT JOIN users u ON s.created_by = u.id 
                    WHERE s.company_id = :cid ORDER BY s.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSupplierById($id) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function createSupplier($data) {
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, company_name, contact_person, phone, email, address, tax_number, current_balance, notes, created_by) 
                    VALUES (:cid, :cname, :cperson, :phone, :email, :address, :tax, :balance, :notes, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':cname', $data['company_name']);
            $this->db->bind(':cperson', $data['contact_person']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':tax', $data['tax_number']);
            $this->db->bind(':balance', $data['current_balance']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateSupplier($data) {
        try {
            $sql = "UPDATE {$this->table} SET 
                    company_name = :cname, 
                    contact_person = :cperson, 
                    phone = :phone, 
                    email = :email, 
                    address = :address, 
                    tax_number = :tax, 
                    notes = :notes 
                    WHERE id = :id AND company_id = :cid";
            
            $this->db->query($sql);
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':cname', $data['company_name']);
            $this->db->bind(':cperson', $data['contact_person']);
            $this->db->bind(':phone', $data['phone']);
            $this->db->bind(':email', $data['email']);
            $this->db->bind(':address', $data['address']);
            $this->db->bind(':tax', $data['tax_number']);
            $this->db->bind(':notes', $data['notes']);
            
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deleteSupplier($id) {
        try {
            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->execute();
        } catch (Exception $e) {
            throw new Exception("لا يمكن حذف المورد لوجود حركات مالية أو فواتير مرتبطة به.");
        }
    }
}