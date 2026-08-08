<?php
// app/models/Contract.php

class Contract extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'contracts';
        
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. إنشاء الجدول بشكل مبدئي إذا لم يكن موجوداً
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `contracts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. إضافة الأعمدة المفقودة تلقائياً (الحل السحري)
        $columnsToAdd = [
            'company_id'      => "INT DEFAULT 1",
            'contract_number' => "VARCHAR(50) NOT NULL DEFAULT 'CTR-000'",
            'title'           => "VARCHAR(255) NOT NULL DEFAULT 'بدون عنوان'",
            'customer_name'   => "VARCHAR(255) DEFAULT NULL",
            'start_date'      => "DATE DEFAULT NULL",
            'end_date'        => "DATE DEFAULT NULL",
            'value'           => "DECIMAL(15,2) DEFAULT 0.00",
            'status'          => "VARCHAR(50) DEFAULT 'draft'",
            'description'     => "TEXT DEFAULT NULL",
            'created_by'      => "INT NOT NULL DEFAULT 0",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `contracts` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `contracts` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {
                // تجاهل بصمت حتى لا يتوقف النظام
            }
        }
    }

    public function getAllContracts(): array {
        $sql = "SELECT c.*, u.name as creator_name 
                FROM {$this->table} c 
                LEFT JOIN users u ON c.created_by = u.id 
                WHERE c.company_id = :cid 
                ORDER BY c.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getContractById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createContract(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (company_id, contract_number, title, customer_name, start_date, end_date, value, status, description, created_by) 
                VALUES (:cid, :cnum, :title, :cname, :sdate, :edate, :value, :status, :desc, :user)";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':cnum', $data['contract_number']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':cname', $data['customer_name'] ?? null);
        $this->db->bind(':sdate', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':value', $data['value'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':user', Session::getUserId());
        
        return $this->db->execute();
    }

    public function updateContract(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET contract_number = :cnum, title = :title, customer_name = :cname, 
                    start_date = :sdate, end_date = :edate, value = :value, 
                    status = :status, description = :desc 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':cnum', $data['contract_number']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':cname', $data['customer_name'] ?? null);
        $this->db->bind(':sdate', !empty($data['start_date']) ? $data['start_date'] : null);
        $this->db->bind(':edate', !empty($data['end_date']) ? $data['end_date'] : null);
        $this->db->bind(':value', $data['value'] ?? 0);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        return $this->db->execute();
    }

    public function deleteContract(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}