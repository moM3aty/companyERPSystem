<?php
// app/models/AssetAssignment.php

class AssetAssignment extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'hr_employee_assets';
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
            'employee_id'     => "INT NOT NULL",
            'asset_id'        => "VARCHAR(100) NOT NULL", // Serial Number or Internal ID
            'asset_type'      => "VARCHAR(100) NOT NULL", // Laptop, Mobile phone, Vehicle, Uniform, Keys, Access card
            'issue_date'      => "DATE NOT NULL",
            'condition_given' => "VARCHAR(100) NULL", // New, Good, Used
            'return_date'     => "DATE NULL",
            'status'          => "VARCHAR(50) DEFAULT 'Assigned'", // Assigned, Returned, Lost, Damaged
            'notes'           => "TEXT NULL",
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

    public function getAllAssets() {
        $sql = "SELECT a.*, e.name as employee_name 
                FROM {$this->table} a 
                JOIN employees e ON a.employee_id = e.id 
                WHERE a.company_id = :cid 
                ORDER BY a.issue_date DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getAssetById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createAsset($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, asset_id, asset_type, issue_date, condition_given, status, notes) 
                VALUES (:cid, :emp, :aid, :type, :idate, :cond, :status, :notes)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':aid', $data['asset_id']);
        $this->db->bind(':type', $data['asset_type']);
        $this->db->bind(':idate', $data['issue_date']);
        $this->db->bind(':cond', $data['condition_given']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        return $this->db->execute();
    }

    public function updateAsset($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, asset_id = :aid, asset_type = :type, 
                    issue_date = :idate, condition_given = :cond, return_date = :rdate, 
                    status = :status, notes = :notes 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':aid', $data['asset_id']);
        $this->db->bind(':type', $data['asset_type']);
        $this->db->bind(':idate', $data['issue_date']);
        $this->db->bind(':cond', $data['condition_given']);
        $this->db->bind(':rdate', !empty($data['return_date']) ? $data['return_date'] : null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteAsset($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}