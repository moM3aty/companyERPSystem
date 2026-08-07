<?php
// المسار: app/models/ProductBatch.php

class ProductBatch extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'product_batches';
    }

    /**
     * جلب جميع التشغيلات لمنتج معين
     */
    public function getBatchesByProduct(int $productId): array {
        $sql = "SELECT pb.*, p.name as product_name 
                FROM {$this->table} pb 
                JOIN products p ON pb.product_id = p.id 
                WHERE pb.product_id = :product_id 
                ORDER BY pb.expiry_date ASC, pb.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * جلب جميع التشغيلات والسيريالات لكل منتجات الشركة (للعرض الشامل)
     */
    public function getAllBatches(): array {
        $sql = "SELECT pb.*, p.name as product_name, p.sku 
                FROM {$this->table} pb 
                JOIN products p ON pb.product_id = p.id 
                WHERE p.company_id = :cid
                ORDER BY pb.expiry_date ASC, pb.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * التحقق من توفر سيريال نمبر (يجب أن يكون فريداً)
     */
    public function isSerialNumberUnique(string $serialNumber): bool {
        if (empty($serialNumber)) return true;
        $this->db->query("SELECT id FROM {$this->table} WHERE serial_number = :sn LIMIT 1");
        $this->db->bind(':sn', $serialNumber);
        $this->db->execute();
        return $this->db->rowCount() === 0;
    }

    /**
     * إضافة دفعة/تشغيلة جديدة
     */
    public function addBatch(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (product_id, lot_number, serial_number, production_date, expiry_date, quantity, status, created_at) 
                VALUES 
                (:product_id, :lot_number, :serial_number, :production_date, :expiry_date, :quantity, :status, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $data['product_id'], PDO::PARAM_INT);
        $this->db->bind(':lot_number', $data['lot_number'] ?? null);
        $this->db->bind(':serial_number', $data['serial_number'] ?? null);
        $this->db->bind(':production_date', $data['production_date'] ?? null);
        $this->db->bind(':expiry_date', $data['expiry_date'] ?? null);
        $this->db->bind(':quantity', $data['quantity'], PDO::PARAM_INT);
        $this->db->bind(':status', $data['status'] ?? 'available');
        
        return $this->db->execute();
    }
}