<?php
// المسار: app/models/Stocktake.php

class Stocktake extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'stock_adjustments';
    }

    /**
     * جلب جميع حركات التسويات والجرد مع بيانات المنتج والمستخدم
     */
    public function getAllAdjustments(): array {
        $sql = "SELECT sa.*, p.name as product_name, p.sku, u.name as created_by_name 
                FROM {$this->table} sa 
                LEFT JOIN products p ON sa.product_id = p.id 
                LEFT JOIN users u ON sa.created_by = u.id 
                ORDER BY sa.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * حفظ حركة التسوية وتحديث كمية المخزون (باستخدام المعاملات Transactions)
     */
    public function createAdjustment(array $data): bool {
        try {
            $this->db->beginTransaction();

            // 1. توليد رقم مرجعي للتسوية
            $referenceNo = 'ADJ-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            // 2. تسجيل حركة التسوية في السجل
            $sqlInsert = "INSERT INTO {$this->table} (reference_no, date, type, product_id, quantity, notes, created_by, created_at) 
                          VALUES (:ref, :date, :type, :product_id, :quantity, :notes, :created_by, NOW())";
            
            $this->db->query($sqlInsert);
            $this->db->bind(':ref', $referenceNo);
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':product_id', $data['product_id'], PDO::PARAM_INT);
            $this->db->bind(':quantity', $data['quantity'], PDO::PARAM_INT);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
            $this->db->execute();

            // 3. تحديث كمية المنتج في المخزون الرئيسي
            if ($data['type'] === 'addition') {
                $sqlUpdate = "UPDATE products SET quantity = quantity + :qty WHERE id = :id";
            } else {
                // للأنواع (subtraction, damage, loss) يتم الخصم من المخزون
                $sqlUpdate = "UPDATE products SET quantity = quantity - :qty WHERE id = :id";
            }

            $this->db->query($sqlUpdate);
            $this->db->bind(':qty', $data['quantity'], PDO::PARAM_INT);
            $this->db->bind(':id', $data['product_id'], PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}