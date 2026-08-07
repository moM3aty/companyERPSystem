<?php
// المسار: app/models/Category.php

class Category extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'categories';
    }

    public function getAllCategories(): array {
        // جلب التصنيفات مع عدد المنتجات المرتبطة بكل تصنيف
        $sql = "SELECT c.*, COUNT(p.id) as products_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.id = p.category_id 
                GROUP BY c.id 
                ORDER BY c.name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createCategory(array $data): bool {
        $sql = "INSERT INTO {$this->table} (name, description, created_at) VALUES (:name, :description, NOW())";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function deleteCategory(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}