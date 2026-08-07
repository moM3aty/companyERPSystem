<?php
// المسار: app/models/Customer.php

class Customer extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'customers';
    }

    /**
     * جلب جميع العملاء مع إحصائياتهم (عدد الفواتير وإجمالي المشتريات)
     */
    public function getAllCustomers(): array {
        $sql = "SELECT c.*, 
                       COUNT(i.id) as invoice_count, 
                       COALESCE(SUM(i.total_amount), 0) as total_purchases
                FROM {$this->table} c
                LEFT JOIN invoices i ON c.id = i.customer_id
                GROUP BY c.id
                ORDER BY c.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب بيانات عميل واحد بالمعرف الخاص به
     */
    public function getCustomerById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * إضافة عميل جديد
     */
    public function createCustomer(array $data): bool {
        $sql = "INSERT INTO {$this->table} (name, phone, email, address, type, balance, created_at) 
                VALUES (:name, :phone, :email, :address, :type, :balance, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':balance', $data['balance']);
        
        return $this->db->execute();
    }

    /**
     * تحديث بيانات العميل
     */
    public function updateCustomer(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, 
                    phone = :phone, 
                    email = :email, 
                    address = :address, 
                    type = :type 
                WHERE id = :id";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    /**
     * حذف العميل
     */
    public function deleteCustomer(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}