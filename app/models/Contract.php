<?php
// app/models/Contract.php

class Contract extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'contracts';
    }

    public function getAllContractsDetails(): array {
        $sql = "SELECT c.*, 
                       CASE 
                           WHEN c.party_type = 'customer' THEN cust.name 
                           WHEN c.party_type = 'supplier' THEN sup.name 
                       END as party_name
                FROM {$this->table} c
                LEFT JOIN customers cust ON c.party_id = cust.id AND c.party_type = 'customer'
                LEFT JOIN suppliers sup ON c.party_id = sup.id AND c.party_type = 'supplier'
                ORDER BY c.end_date ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createContractDetails(array $data): bool {
        $sql = "INSERT INTO {$this->table} (contract_number, title, party_type, party_id, start_date, end_date, value, status, description, created_at)
                VALUES (:contract_number, :title, :party_type, :party_id, :start_date, :end_date, :value, :status, :description, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':contract_number', $data['contract_number']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':party_type', $data['party_type']);
        $this->db->bind(':party_id', $data['party_id'], PDO::PARAM_INT);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':value', $data['value']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':description', $data['description']);
        
        if ($this->db->execute()) {
            $contractId = $this->db->lastInsertId();
            ActivityLog::logAction('CREATE', 'Contracts', $contractId, "تم تسجيل عقد جديد: {$data['title']}");
            return true;
        }
        return false;
    }

    public function deleteContract(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        if ($this->db->execute()) {
            ActivityLog::logAction('DELETE', 'Contracts', $id, "تم حذف العقد نهائياً");
            return true;
        }
        return false;
    }
}