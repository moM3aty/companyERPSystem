<?php
// المسار: app/models/SaleReturn.php

class SaleReturn extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_returns';
    }

    public function getAllReturns(): array {
        $sql = "SELECT sr.*, i.invoice_number, i.customer_name, u.name as processed_by 
                FROM {$this->table} sr 
                JOIN invoices i ON sr.invoice_id = i.id 
                LEFT JOIN users u ON sr.created_by = u.id 
                ORDER BY sr.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createSaleReturn(array $returnData, array $items): bool {
        try {
            $this->db->beginTransaction();

            $returnNumber = 'RET-' . date('Ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            $sqlMain = "INSERT INTO {$this->table} (return_number, invoice_id, total_refund, reason, created_by, created_at) 
                        VALUES (:return_number, :invoice_id, :total_refund, :reason, :created_by, NOW())";
            $this->db->query($sqlMain);
            $this->db->bind(':return_number', $returnNumber);
            $this->db->bind(':invoice_id', $returnData['invoice_id'], PDO::PARAM_INT);
            $this->db->bind(':total_refund', $returnData['total_refund']);
            $this->db->bind(':reason', $returnData['reason']);
            $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
            $this->db->execute();

            $returnId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO sales_return_items (return_id, product_id, quantity, price, subtotal) 
                        VALUES (:return_id, :product_id, :quantity, :price, :subtotal)";
            $sqlRestoreStock = "UPDATE products SET quantity = quantity + :qty WHERE id = :pid";

            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':return_id', $returnId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                $this->db->query($sqlRestoreStock);
                $this->db->bind(':qty', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':pid', $item['product_id'], PDO::PARAM_INT);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}