<?php
// app/models/Product.php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
    }

    public function getAllProducts(): array {
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.company_id = :cid 
                ORDER BY p.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // إضافة الدالة المفقودة للـ Controller مع دعم عزل الـ SaaS والربط مع التصنيفات
    public function getProductsWithCategory(): array {
        return $this->getAllProducts(); // هي نفسها الدالة السابقة لأنها تجلب التصنيف بالفعل
    }

    public function findById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function count(): int {
        $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE company_id = :cid");
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $row = $this->db->single();
        return (int)($row->total ?? 0);
    }

    public function createProduct(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (company_id, category_id, name, sku, description, price, cost, quantity, reorder_point, track_batches) 
                VALUES (:cid, :cat, :name, :sku, :desc, :price, :cost, :qty, :reorder, :track)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':cat', $data['category_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost', $data['cost']);
        $this->db->bind(':qty', $data['quantity'], PDO::PARAM_INT);
        $this->db->bind(':reorder', $data['reorder_point'], PDO::PARAM_INT);
        $this->db->bind(':track', $data['track_batches'], PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            ActivityLog::logAction('CREATE', 'Products', $this->db->lastInsertId(), "إضافة منتج جديد: {$data['name']}");
            return true;
        }
        return false;
    }

    public function updateProduct(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET category_id = :cat, name = :name, sku = :sku, description = :desc, 
                    price = :price, cost = :cost, quantity = :qty, reorder_point = :reorder, track_batches = :track 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':cat', $data['category_id'] ?: null, PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost', $data['cost']);
        $this->db->bind(':qty', $data['quantity'], PDO::PARAM_INT);
        $this->db->bind(':reorder', $data['reorder_point'], PDO::PARAM_INT);
        $this->db->bind(':track', $data['track_batches'], PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        
        return $this->db->execute();
    }

    public function deleteProduct(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->execute();
    }
}