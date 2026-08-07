<?php
// app/models/Customer.php

class Customer extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'customers';
    }

    public function getAllCustomers(): array {
        // إذا كان المالك الشامل (null)، نعتبره يعمل على الشركة رقم 1 افتراضياً
        $cid = Session::get('company_id') ?: 1; 
        
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY id DESC");
        $this->db->bind(':cid', $cid, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getCustomerById(int $id): ?object {
        $cid = Session::get('company_id') ?: 1;
        
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', $cid, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function createCustomer(array $data): bool {
        $cid = Session::get('company_id') ?: 1;
        
        $sql = "INSERT INTO {$this->table} (company_id, name, email, phone, address, company, balance, created_at) 
                VALUES (:cid, :name, :email, :phone, :address, :company, :balance, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', $cid, PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':company', $data['company']);
        $this->db->bind(':balance', $data['balance'] ?? 0);
        
        if($this->db->execute()){
            ActivityLog::logAction('CREATE', 'Customers', $this->db->lastInsertId(), "إضافة عميل جديد: {$data['name']}");
            return true;
        }
        return false;
    }

    public function updateCustomer(int $id, array $data): bool {
        $cid = Session::get('company_id') ?: 1;
        
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
        $this->db->bind(':cid', $cid, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteCustomer(int $id): bool {
        $cid = Session::get('company_id') ?: 1;
        
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', $cid, PDO::PARAM_INT);
        return $this->db->execute();
    }
}