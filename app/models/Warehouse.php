<?php
    // app/models//Warehouse.php

class Warehouse extends Model {
    protected string $table = 'warehouses';
    
    /**
     * نقل مخزون بين مستودعين
     */
    public function transferStock($fromWh, $toWh, $productId, $quantity, $requestedBy, $notes = '') {
        $this->db->beginTransaction();
        try {
            // التأكد من وجود الكمية في المستودع المصدر
            $this->db->query('
                SELECT quantity FROM warehouse_stock 
                WHERE product_id = :prod AND warehouse_id = :wh
            ');
            $this->db->bind(':prod', $productId, PDO::PARAM_INT);
            $this->db->bind(':wh', $fromWh, PDO::PARAM_INT);
            $row = $this->db->single();
            if (!$row || $row->quantity < $quantity) {
                throw new Exception('الكمية غير متوفرة في المستودع المصدر');
            }
            
            // إنشاء طلب نقل
            $transferNumber = 'TR-' . date('YmdHis');
            $this->db->query('
                INSERT INTO stock_transfers 
                (transfer_number, from_warehouse_id, to_warehouse_id, product_id, quantity, requested_by, notes)
                VALUES (:num, :from, :to, :prod, :qty, :req, :notes)
            ');
            $this->db->bind(':num', $transferNumber);
            $this->db->bind(':from', $fromWh, PDO::PARAM_INT);
            $this->db->bind(':to', $toWh, PDO::PARAM_INT);
            $this->db->bind(':prod', $productId, PDO::PARAM_INT);
            $this->db->bind(':qty', $quantity);
            $this->db->bind(':req', $requestedBy, PDO::PARAM_INT);
            $this->db->bind(':notes', $notes);
            $this->db->execute();
            $transferId = $this->db->lastInsertId();
            
            // تنفيذ النقل (خصم من المصدر + إضافة إلى الوجهة)
            $this->db->query('
                UPDATE warehouse_stock 
                SET quantity = quantity - :qty 
                WHERE product_id = :prod AND warehouse_id = :wh
            ');
            $this->db->bind(':qty', $quantity);
            $this->db->bind(':prod', $productId, PDO::PARAM_INT);
            $this->db->bind(':wh', $fromWh, PDO::PARAM_INT);
            $this->db->execute();
            
            // إضافة إلى المستودع الوجهة (أو إنشاء سجل)
            $this->db->query('
                INSERT INTO warehouse_stock (product_id, warehouse_id, quantity)
                VALUES (:prod, :wh, :qty)
                ON DUPLICATE KEY UPDATE quantity = quantity + :qty
            ');
            $this->db->bind(':prod', $productId, PDO::PARAM_INT);
            $this->db->bind(':wh', $toWh, PDO::PARAM_INT);
            $this->db->bind(':qty', $quantity);
            $this->db->execute();
            
            // تحديث حالة النقل إلى completed
            $this->db->query('
                UPDATE stock_transfers 
                SET status = "completed", completed_at = NOW()
                WHERE id = :id
            ');
            $this->db->bind(':id', $transferId, PDO::PARAM_INT);
            $this->db->execute();
            
            $this->db->commit();
            return $transferId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}