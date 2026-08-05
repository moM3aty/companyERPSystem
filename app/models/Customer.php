<?php
// app/models/Customer.php

class Customer extends Model {
    
    protected string $table = 'customers';
    protected string $primaryKey = 'id';
    
    /**
     * جلب كل العملاء مع عدد الفواتير والمشتريات
     */
    public function getCustomers() {
        $this->db->query('
            SELECT c.*,
                   COUNT(DISTINCT i.id) as invoice_count,
                   COALESCE(SUM(i.total_amount), 0) as total_purchases
            FROM customers c
            LEFT JOIN invoices i ON c.id = i.customer_id
            GROUP BY c.id
            ORDER BY c.id DESC
        ');
        return $this->db->resultSet();
    }
    
    /**
     * جلب عميل واحد بالمعرّف
     */
    public function getCustomerById($id) {
        $this->db->query('
            SELECT c.*,
                   COUNT(DISTINCT i.id) as invoice_count,
                   COALESCE(SUM(i.total_amount), 0) as total_purchases
            FROM customers c
            LEFT JOIN invoices i ON c.id = i.customer_id
            WHERE c.id = :id
            GROUP BY c.id
        ');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    
    /**
     * البحث في العملاء بالاسم أو الهاتف
     */
    public function searchCustomers($query) {
        $this->db->query('
            SELECT id, name, phone, type, balance 
            FROM customers 
            WHERE name LIKE :q OR phone LIKE :q
            ORDER BY name ASC
            LIMIT 50
        ');
        $this->db->bind(':q', '%' . $query . '%');
        return $this->db->resultSet();
    }
    
    /**
     * جلب عدد العملاء
     */
    public function getCustomerCount() {
        $this->db->query("SELECT COUNT(*) as total FROM customers");
        $result = $this->db->single();
        return $result ? (int) $result->total : 0;
    }
    
    /**
     * جلب إجمالي الذمم المدينة
     */
    public function getTotalReceivables() {
        $this->db->query("
            SELECT COALESCE(SUM(balance), 0) as total 
            FROM customers 
            WHERE balance > 0
        ");
        $result = $this->db->single();
        return $result ? (float) $result->total : 0.0;
    }
    
    /**
     * إضافة عميل جديد
     */
    public function addCustomer($data) {
        $this->db->query('
            INSERT INTO customers 
                (name, email, phone, address, type, balance, notes)
            VALUES 
                (:name, :email, :phone, :address, :type, :balance, :notes)
        ');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'individual');
        $this->db->bind(':balance', $data['balance'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->execute();
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * تحديث بيانات عميل
     */
    public function updateCustomer($data, $id) {
        $this->db->query('
            UPDATE customers SET 
                name = :name, 
                email = :email, 
                phone = :phone, 
                address = :address, 
                type = :type, 
                notes = :notes
            WHERE id = :id
        ');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'individual');
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
    
    /**
     * حذف عميل
     */
    public function deleteCustomer($id) {
        // التحقق من وجود فواتير قيد التنفيذ
        $this->db->query("SELECT COUNT(*) as cnt FROM invoices WHERE customer_id = :cid");
        $this->db->bind(':cid', $id, PDO::PARAM_INT);
        $result = $this->db->single();
        
        if ($result && $result->cnt > 0) {
            throw new \Exception('لا يمكن حذف العميل - لديه ' . $result->cnt . ' فاتورة قيد التنفيذ');
        }
        
        return parent::delete($id);
    }
    
    /**
     * جلب عدد فواتير العميل
     */
    private function getInvoiceCount($customerId) {
        $this->db->query("
            SELECT COUNT(*) as cnt 
            FROM invoices 
            WHERE customer_id = :cid
        ");
        $this->db->bind(':cid', $customerId, PDO::PARAM_INT);
        $result = $this->db->single();
        return $result ? (int) $result->cnt : 0;
    }
    
    /**
     * جلب أعلى العملاء شراءً (للتقاريرات)
     */
    public function getTopCustomers($limit = 10) {
        $this->db->query('
            SELECT c.id, c.name, c.type,
                   COUNT(DISTINCT i.id) as invoice_count,
                   COALESCE(SUM(i.total_amount), 0) as total_purchases
            FROM customers c
            LEFT JOIN invoices i ON c.id = i.customer_id
            GROUP BY c.id
            ORDER BY total_purchases DESC
            LIMIT :lim
        ');
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}