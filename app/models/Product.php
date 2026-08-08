<?php
// app/models/Product.php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'products';
        
        // فحص وتحديث جدول قاعدة البيانات آلياً بصمت
        $this->autoUpgradeTable();
    }

    /**
     * دالة ذكية لفحص وإضافة الأعمدة الناقصة واحداً تلو الآخر لتجنب انهيار الـ SQL
     */
    private function autoUpgradeTable() {
        $columnsToAdd = [
            'description'   => "TEXT NULL",
            'barcode'       => "VARCHAR(100) NULL",
            'unit'          => "VARCHAR(50) DEFAULT 'قطعة'",
            'cost'          => "DECIMAL(10,2) DEFAULT 0.00",
            'quantity'      => "INT DEFAULT 0",
            'reorder_point' => "INT DEFAULT 5",
            'track_batches' => "TINYINT(1) DEFAULT 0"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                // التحقق مما إذا كان العمود موجوداً
                $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '{$colName}'");
                $exists = $this->db->resultSet();
                
                // إذا لم يكن موجوداً، قم بإضافته
                if (empty($exists)) {
                    $this->db->query("ALTER TABLE {$this->table} ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {
                // تجاهل الأخطاء بصمت كي لا يتوقف النظام
            }
        }
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

    public function getProductsWithCategory(): array {
        return $this->getAllProducts();
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
    
    public function skuExists(string $sku, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE sku = :sku AND company_id = :cid";
        if ($excludeId) $sql .= " AND id != :exclude_id";
        
        $this->db->query($sql);
        $this->db->bind(':sku', $sku);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        if ($excludeId) $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public function getCategories(): array {
        $this->db->query("SELECT id, name FROM categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    public function createProduct(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (company_id, category_id, name, sku, barcode, unit, description, price, cost, quantity, reorder_point, track_batches) 
                VALUES (:cid, :cat, :name, :sku, :barcode, :unit, :desc, :price, :cost, :qty, :reorder, :track)";
        
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':cat', !empty($data['category_id']) ? $data['category_id'] : null, PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':barcode', $data['barcode'] ?? null);
        $this->db->bind(':unit', $data['unit'] ?? 'قطعة');
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost', $data['cost'] ?? 0);
        $this->db->bind(':qty', $data['quantity'] ?? 0, PDO::PARAM_INT);
        $this->db->bind(':reorder', $data['reorder_point'] ?? 0, PDO::PARAM_INT);
        $this->db->bind(':track', $data['track_batches'] ?? 0, PDO::PARAM_INT);
        
        if ($this->db->execute()) {
            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('CREATE', 'Products', $this->db->lastInsertId(), "إضافة منتج جديد للمخزون: {$data['name']}");
            }
            return true;
        }
        return false;
    }

    public function updateProduct(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET category_id = :cat, name = :name, sku = :sku, barcode = :barcode, unit = :unit, 
                    description = :desc, price = :price, cost = :cost, quantity = :qty, 
                    reorder_point = :reorder, track_batches = :track 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':cat', !empty($data['category_id']) ? $data['category_id'] : null, PDO::PARAM_INT);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':barcode', $data['barcode'] ?? null);
        $this->db->bind(':unit', $data['unit'] ?? 'قطعة');
        $this->db->bind(':desc', $data['description'] ?? null);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost', $data['cost'] ?? 0);
        $this->db->bind(':qty', $data['quantity'] ?? 0, PDO::PARAM_INT);
        $this->db->bind(':reorder', $data['reorder_point'] ?? 0, PDO::PARAM_INT);
        $this->db->bind(':track', $data['track_batches'] ?? 0, PDO::PARAM_INT);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        
        if($this->db->execute()) {
            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('UPDATE', 'Products', $id, "تعديل بيانات المنتج: {$data['name']}");
            }
            return true;
        }
        return false;
    }

    public function deleteProduct(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        
        if($this->db->execute()) {
            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('DELETE', 'Products', $id, "حذف صنف من النظام");
            }
            return true;
        }
        return false;
    }
}