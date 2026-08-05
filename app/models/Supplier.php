<?php
// app/models/Supplier.php

class Supplier extends Model {
    
    protected string $table = 'suppliers';
    protected string $primaryKey = 'id';
    
    /**
     * جلب كل الموردين مع عدد أوامر الشراء والإجمالي
     */
    public function getSuppliers() {
        $this->db->query('
            SELECT s.*,
                   COUNT(DISTINCT po.id) as po_count,
                   COALESCE(SUM(po.total_amount), 0) as total_purchases
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.id = po.supplier_id 
                AND po.status NOT IN ("cancelled", "rejected")
            GROUP BY s.id
            ORDER BY s.id DESC
        ');
        return $this->db->resultSet();
    }
    
    /**
     * جلب مورد واحد بالمعرّف
     */
    public function getSupplierById($id) {
        $this->db->query('
            SELECT s.*,
                   COUNT(DISTINCT po.id) as po_count,
                   COALESCE(SUM(po.total_amount), 0) as total_purchases
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.id = po.supplier_id 
                AND po.status NOT IN ("cancelled", "rejected")
            WHERE s.id = :id
            GROUP BY s.id
        ');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    
    /**
     * البحث في الموردين
     */
    public function searchSuppliers($query) {
        $this->db->query('
            SELECT id, name, phone, type, balance
            FROM suppliers
            WHERE name LIKE :q OR phone LIKE :q
            ORDER BY name ASC
            LIMIT 50
        ');
        $this->db->bind(':q', '%' . $query . '%');
        return $this->db->resultSet();
    }
    
    /**
     * جلب عدد الموردين
     */
    public function getSupplierCount() {
        $this->db->query("SELECT COUNT(*) as total FROM suppliers");
        $result = $this->db->single();
        return $result ? (int) $result->total : 0;
    }
    
    /**
     * إضافة مورد جديد
     */
    public function addSupplier($data) {
        $this->db->query('
            INSERT INTO suppliers 
                (name, contact_person, phone, email, address, balance, notes, type)
            VALUES 
                (:name, :contact, :phone, :email, :address, :balance, :notes, :type)
        ');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact', $data['contact_person'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':balance', $data['balance'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'company');
        $this->db->execute();
        
        return (int) $this->db->lastInsertId();
    }
    
    /**
     * تحديث بيانات مورد
     */
    public function updateSupplier($data, $id) {
        $this->db->query('
            UPDATE suppliers SET 
                name = :name, 
                contact_person = :contact, 
                phone = :phone, 
                email = :email, 
                address = :address, 
                notes = :notes, 
                type = :type
            WHERE id = :id
        ');
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact', $data['contact_person'] ?? null);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':email', $data['email'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':type', $data['type'] ?? 'company');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }
    
    /**
     * حذف مورد (يتحقق من أوامر الشراء النشط)
     */
    public function deleteSupplier($id) {
        // التحقق من وجود أوامر شراء نشط
        $this->db->query("
            SELECT COUNT(*) as cnt 
            FROM purchase_orders 
            WHERE supplier_id = :sid 
              AND status NOT IN ('cancelled', 'rejected')
        ");
        $this->db->bind(':sid', $id, PDO::PARAM_INT);
        $result = $this->db->single();
        
        if ($result && $result->cnt > 0) {
            throw new \Exception('لا يمكن حذف المورد - لديه ' . $result->cnt . ' أمر شراء نشط');
        }
        
        return parent::delete($id);
    }
    
    /**
     * جلب إجمالي المشتريات (رصيدنا لدى الموردين)
     */
    public function getTotalPayables() {
        $this->db->query("
            SELECT COALESCE(SUM(po.total_amount), 0) as total
            FROM purchase_orders po
            WHERE po.status NOT IN ('cancelled', 'rejected')
        ");
        $result = $this->db->single();
        return $result ? (float) $result->total : 0.0;
    }
    
    /**
     * جلب أعلى الموردين شراءً
     */
    public function getTopSuppliers($limit = 10) {
        $this->db->query('
            SELECT s.id, s.name, s.type, s.phone,
                   COUNT(DISTINCT po.id) as po_count,
                   COALESCE(SUM(po.total_amount), 0) as total_purchases
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.id = po.supplier_id 
                AND po.status NOT IN ("cancelled", "rejected")
            GROUP BY s.id
            ORDER BY total_purchases DESC
            LIMIT :lim
        ');
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}