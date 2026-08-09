<?php
// app/models/Category.php

class Category extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'categories';
        $this->autoUpgradeTable();
    }

    /* STREAMING_CHUNK: Auto-upgrading tables... */
    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'  => "INT DEFAULT 1",
            'name'        => "VARCHAR(255) NOT NULL",
            'description' => "TEXT DEFAULT NULL",
            'created_at'  => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$colName}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    /* STREAMING_CHUNK: Database Operations... */
    public function getAllCategories(): array {
        // يجلب التصنيفات مع إحصاء عدد المنتجات المرتبطة بكل تصنيف
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) as products_count 
                FROM {$this->table} c 
                WHERE c.company_id = :cid 
                ORDER BY c.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function findById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createCategory(array $data): bool {
        $sql = "INSERT INTO {$this->table} (company_id, name, description, created_at) 
                VALUES (:cid, :name, :desc, NOW())";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':desc', $data['description'] ?? null);
        return $this->db->execute();
    }

    public function update(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET name = :name, description = :desc 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteCategory(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}