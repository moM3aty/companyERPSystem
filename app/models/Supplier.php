<?php
// app/models/Supplier.php

class Supplier extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'suppliers';
    }

    public function getAllSuppliers(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getTotalPayables(): float {
        $this->db->query("SELECT COALESCE(SUM(balance), 0) as total FROM {$this->table} WHERE company_id = :cid AND balance > 0");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $row = $this->db->single();
        return (float)($row->total ?? 0);
    }

    // إضافة الدالة المفقودة للـ Controller مع دعم عزل الـ SaaS
    public function getFilteredSuppliers(string $search = ''): array {
        if(empty($search)) {
            return $this->getAllSuppliers();
        }
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid AND (name LIKE :search OR company LIKE :search OR phone LIKE :search) ORDER BY id DESC");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':search', '%' . $search . '%');
        return $this->db->resultSet();
    }

    public function getSupplierById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createSupplier(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, email, phone, address, company, balance, created_at) 
                VALUES (:cid, :name, :email, :phone, :address, :company, :balance, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':balance', $data['balance'] ?? 0);
        
        if($this->db->execute()){
            ActivityLog::logAction('CREATE', 'Suppliers', $this->db->lastInsertId(), "إضافة مورد جديد: {$data['name']}");
            return true;
        }
        return false;
    }

    public function updateSupplier(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, email = :email, phone = :phone, address = :address, company = :company, balance = :balance 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':balance', $data['balance']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteSupplier(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }
}