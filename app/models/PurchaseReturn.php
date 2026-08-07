<?php
// المسار: app/models/PurchaseReturn.php

class PurchaseReturn extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_returns';
    }

    public function getAllReturns(): array {
        $sql = "SELECT pr.*, s.name as supplier_name, u.name as created_by_name, po.po_number 
                FROM {$this->table} pr 
                LEFT JOIN suppliers s ON pr.supplier_id = s.id 
                LEFT JOIN users u ON pr.created_by = u.id 
                LEFT JOIN purchase_orders po ON pr.po_id = po.id 
                ORDER BY pr.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createReturn(array $data, array $items): bool {
        try {
            $this->db->beginTransaction();

            $returnNumber = 'PRET-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            $sqlMain = "INSERT INTO {$this->table} (return_number, po_id, supplier_id, total_refund, reason, created_by, created_at) 
                        VALUES (:return_number, :po_id, :supplier_id, :total_refund, :reason, :created_by, NOW())";
            $this->db->query($sqlMain);
            $this->db->bind(':return_number', $returnNumber);
            $this->db->bind(':po_id', $data['po_id'] ?: null, PDO::PARAM_INT);
            $this->db->bind(':supplier_id', $data['supplier_id'], PDO::PARAM_INT);
            $this->db->bind(':total_refund', $data['total_refund']);
            $this->db->bind(':reason', $data['reason']);
            $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
            $this->db->execute();

            $returnId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO purchase_return_items (return_id, product_id, quantity, price, subtotal) 
                        VALUES (:return_id, :product_id, :quantity, :price, :subtotal)";
            $sqlDeductStock = "UPDATE products SET quantity = quantity - :qty WHERE id = :pid AND quantity >= :qty";

            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':return_id', $returnId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                $this->db->query($sqlDeductStock);
                $this->db->bind(':qty', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();
                
                if ($this->db->rowCount() === 0) {
                    throw new Exception("المخزون غير كافٍ.");
                }
            }

            // تخفيض رصيد المورد (لأننا أعدنا له بضاعة)
            $sqlSupplier = "UPDATE suppliers SET balance = balance - :refund WHERE id = :supplier_id";
            $this->db->query($sqlSupplier);
            $this->db->bind(':refund', $data['total_refund']);
            $this->db->bind(':supplier_id', $data['supplier_id'], PDO::PARAM_INT);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}