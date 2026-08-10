<?php
// app/models/HrDocument.php

class HrDocument extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'hr_employee_documents';
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
            'company_id'        => "INT DEFAULT 1",
            'employee_id'       => "INT NOT NULL",
            'doc_type'          => "VARCHAR(100) NOT NULL", // National ID, Passport, Iqama, Visa, Driving License...
            'doc_number'        => "VARCHAR(100) NOT NULL",
            'issue_date'        => "DATE NULL",
            'expiry_date'       => "DATE NULL",
            'issuing_authority' => "VARCHAR(150) NULL",
            'attachment'        => "VARCHAR(255) NULL",
            'created_at'        => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

    public function getAllDocuments() {
        $sql = "SELECT d.*, e.name as employee_name, DATEDIFF(d.expiry_date, CURDATE()) as days_to_expire 
                FROM {$this->table} d 
                JOIN employees e ON d.employee_id = e.id 
                WHERE d.company_id = :cid 
                ORDER BY d.expiry_date ASC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getDocumentById($id) {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createDocument($data) {
        $sql = "INSERT INTO {$this->table} 
                (company_id, employee_id, doc_type, doc_number, issue_date, expiry_date, issuing_authority, attachment) 
                VALUES (:cid, :emp, :type, :num, :idate, :edate, :auth, :attach)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':type', $data['doc_type']);
        $this->db->bind(':num', $data['doc_number']);
        $this->db->bind(':idate', !empty($data['issue_date']) ? $data['issue_date'] : null);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':auth', $data['issuing_authority']);
        $this->db->bind(':attach', $data['attachment']);
        return $this->db->execute();
    }

    public function updateDocument($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET employee_id = :emp, doc_type = :type, doc_number = :num, 
                    issue_date = :idate, expiry_date = :edate, issuing_authority = :auth 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':emp', $data['employee_id']);
        $this->db->bind(':type', $data['doc_type']);
        $this->db->bind(':num', $data['doc_number']);
        $this->db->bind(':idate', !empty($data['issue_date']) ? $data['issue_date'] : null);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':auth', $data['issuing_authority']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteDocument($id) {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}