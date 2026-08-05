<?php
// app/models/Product.php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
        // تحديد اسم الجدول في قاعدة البيانات
        $this->table = 'products';
    }

    /**
     * جلب جميع المنتجات مع ربطها بجدول التصنيفات لجلب اسم التصنيف
     * 
     * @return array مصفوفة كائنات المنتجات
     */
    public function getProductsWithCategory(): array {
        $sql = "SELECT p.*, c.name as cat_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب قائمة التصنيفات (Categories) المتاحة للاختيار في نموذج إضافة منتج
     * 
     * @return array مصفوفة كائنات التصنيفات
     */
    public function getCategories(): array {
        $this->db->query("SELECT id, name FROM categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * التحقق من عدم تكرار رمز المنتج (SKU) في قاعدة البيانات
     * 
     * @param string $sku رمز المنتج المراد فحصه
     * @param int|null $excludeId استثناء معرف معين (يستخدم عند التعديل)
     * @return bool يرجع true إذا كان موجوداً، و false إذا كان غير موجود
     */
    public function skuExists(string $sku, ?int $excludeId = null): bool {
        $sql = "SELECT id FROM {$this->table} WHERE sku = :sku";
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':sku', $sku);
        
        if ($excludeId !== null) {
            $this->db->bind(':exclude_id', $excludeId, PDO::PARAM_INT);
        }
        
        $this->db->execute();
        
        return $this->db->rowCount() > 0;
    }
}