<?php
class Purchase extends Model {
    
    /**
     * إنشاء أمر شراء مع أصنافه
     */
    public function createPurchaseOrder($supplierId, $items, $notes = '') {
        // $items = [['product_id'=>1, 'quantity'=>5, 'unit_price'=>100], ...]
        $this->db->beginTransaction();
        try {
            // رقم الأمر
            $poNumber = 'PO-' . date('YmdHis');
            // حساب الإجمالي
            $total = 0;
            foreach ($items as $item) {
                $total += $item['quantity'] * $item['unit_price'];
            }
            
            // إدراج الأمر
            $this->db->query('
                INSERT INTO purchase_orders 
                (po_number, supplier_id, total_amount, notes, status)
                VALUES (:po, :supplier, :total, :notes, "pending")
            ');
            $this->db->bind(':po', $poNumber);
            $this->db->bind(':supplier', $supplierId, PDO::PARAM_INT);
            $this->db->bind(':total', $total);
            $this->db->bind(':notes', $notes);
            $this->db->execute();
            $poId = $this->db->lastInsertId();
            
            // إدراج الأصناف
            foreach ($items as $item) {
                $this->db->query('
                    INSERT INTO purchase_order_items 
                    (po_id, product_id, quantity_ordered, unit_price, total)
                    VALUES (:po, :prod, :qty, :price, :total)
                ');
                $this->db->bind(':po', $poId, PDO::PARAM_INT);
                $this->db->bind(':prod', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':total', $item['quantity'] * $item['unit_price']);
                $this->db->execute();
            }
            
            $this->db->commit();
            return $poId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * استلام بضاعة (تحديث الكميات في المخزون)
     */
    public function receiveGoods($poId, $receivedItems) {
        // $receivedItems = [['product_id'=>1, 'quantity_received'=>5], ...]
        $this->db->beginTransaction();
        try {
            // تحديث كمية المستلم في جدول الأصناف
            foreach ($receivedItems as $item) {
                $this->db->query('
                    UPDATE purchase_order_items 
                    SET quantity_received = quantity_received + :qty
                    WHERE po_id = :po AND product_id = :prod
                ');
                $this->db->bind(':qty', $item['quantity_received']);
                $this->db->bind(':po', $poId, PDO::PARAM_INT);
                $this->db->bind(':prod', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();
                
                // زيادة مخزون المنتج (في المستودع الافتراضي أو حسب تعيين)
                $this->db->query('
                    UPDATE products SET quantity = quantity + :qty WHERE id = :prod
                ');
                $this->db->bind(':qty', $item['quantity_received']);
                $this->db->bind(':prod', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();
            }
            
            // تغيير حالة الأمر إلى "تم التسليم"
            $this->db->query('
                UPDATE purchase_orders 
                SET status = "delivered", received_date = CURDATE()
                WHERE id = :po
            ');
            $this->db->bind(':po', $poId, PDO::PARAM_INT);
            $this->db->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}