<?php
// app/models/Product.php

class Product extends Model {
    
    // جلب جميع المنتجات مع اسم التصنيف
    public function getProducts() {
        $this->db->query('SELECT p.*, c.name as cat_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          ORDER BY p.id DESC');
        return $this->db->resultSet();
    }

    public function getProductById($id) {
        $this->db->query('SELECT * FROM products WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function addProduct($data) {
        $this->db->query('INSERT INTO products (name, sku, category_id, quantity, price) 
                          VALUES (:name, :sku, :category_id, :quantity, :price)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':price', $data['price']);
        return $this->db->execute();
    }

    public function updateProduct($data, $id) {
        $this->db->query('UPDATE products SET 
                          name = :name, sku = :sku, category_id = :category_id, 
                          quantity = :quantity, price = :price 
                          WHERE id = :id');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function deleteProduct($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getCategories() {
        $this->db->query('SELECT * FROM categories ORDER BY name ASC');
        return $this->db->resultSet();
    }
}