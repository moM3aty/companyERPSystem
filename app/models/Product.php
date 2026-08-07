<?php
// المسار: app/models/Product.php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
    }

    public function getProductsWithCategory(): array {
        $sql = "SELECT p.*, c.name as cat_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getCategories(): array {
        $this->db->query("SELECT id, name FROM categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function skuExists(string $sku, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE sku = :sku";
        if ($excludeId !== null) $sql .= " AND id != :exclude_id";
        
        $this->db->query($sql);
        $this->db->bind(':sku', $sku);
        if ($excludeId !== null) $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }

    public function barcodeExists(string $barcode, ?int $excludeId = null): bool {
        if (empty($barcode)) return false;
        $sql = "SELECT id FROM {$this->table} WHERE barcode = :barcode";
        if ($excludeId !== null) $sql .= " AND id != :exclude_id";
        
        $this->db->query($sql);
        $this->db->bind(':barcode', $barcode);
        if ($excludeId !== null) $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }

    public function createProduct(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (name, unit, sku, barcode, category_id, quantity, reorder_point, track_batches, price, created_at) 
                VALUES 
                (:name, :unit, :sku, :barcode, :category_id, :quantity, :reorder_point, :track_batches, :price, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':unit', $data['unit']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':barcode', empty($data['barcode']) ? null : $data['barcode']);
        $this->db->bind(':category_id', $data['category_id'], PDO::PARAM_INT);
        $this->db->bind(':quantity', $data['quantity'], PDO::PARAM_INT);
        $this->db->bind(':reorder_point', $data['reorder_point'], PDO::PARAM_INT);
        $this->db->bind(':track_batches', $data['track_batches'], PDO::PARAM_INT);
        $this->db->bind(':price', $data['price']);
        
        return $this->db->execute();
    }

    // --- دالة التعديل (Edit) الجديدة ---
    public function updateProduct(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, unit = :unit, sku = :sku, barcode = :barcode, 
                    category_id = :category_id, quantity = :quantity, 
                    reorder_point = :reorder_point, track_batches = :track_batches, price = :price 
                WHERE id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':unit', $data['unit']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':barcode', empty($data['barcode']) ? null : $data['barcode']);
        $this->db->bind(':category_id', $data['category_id'], PDO::PARAM_INT);
        $this->db->bind(':quantity', $data['quantity'], PDO::PARAM_INT);
        $this->db->bind(':reorder_point', $data['reorder_point'], PDO::PARAM_INT);
        $this->db->bind(':track_batches', $data['track_batches'], PDO::PARAM_INT);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function findById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function delete(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}