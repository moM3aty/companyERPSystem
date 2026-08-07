<?php
// المسار: app/models/SalesOrder.php

class SalesOrder extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_orders';
    }

    public function getAllOrders(): array {
        $sql = "SELECT so.*, c.name as customer_name, u.name as creator_name 
                FROM {$this->table} so 
                LEFT JOIN customers c ON so.customer_id = c.id 
                LEFT JOIN users u ON so.created_by = u.id 
                ORDER BY so.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getOrderById(int $id): ?object {
        $sql = "SELECT so.*, c.name as customer_name, c.phone, c.address 
                FROM {$this->table} so 
                LEFT JOIN customers c ON so.customer_id = c.id 
                WHERE so.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getOrderItems(int $orderId): array {
        $sql = "SELECT soi.*, p.name as product_name, p.sku 
                FROM sales_order_items soi 
                LEFT JOIN products p ON soi.product_id = p.id 
                WHERE soi.order_id = :order_id";
        $this->db->query($sql);
        $this->db->bind(':order_id', $orderId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createOrder(array $orderData, array $items): bool {
        try {
            $this->db->beginTransaction();

            $orderNumber = 'SO-' . date('ymd') . '-' . str_pad((string)random_int(100, 999), 3, '0', STR_PAD_LEFT);

            $sqlMain = "INSERT INTO {$this->table} (order_number, customer_id, order_date, status, total_amount, notes, created_by, created_at) 
                        VALUES (:order_number, :customer_id, :order_date, :status, :total_amount, :notes, :created_by, NOW())";
            
            $this->db->query($sqlMain);
            $this->db->bind(':order_number', $orderNumber);
            $this->db->bind(':customer_id', $orderData['customer_id'], PDO::PARAM_INT);
            $this->db->bind(':order_date', $orderData['order_date']);
            $this->db->bind(':status', $orderData['status']);
            $this->db->bind(':total_amount', $orderData['total_amount']);
            $this->db->bind(':notes', $orderData['notes']);
            $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
            $this->db->execute();

            $orderId = $this->db->lastInsertId();

            $sqlItems = "INSERT INTO sales_order_items (order_id, product_id, quantity, price, subtotal) 
                         VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItems);
                $this->db->bind(':order_id', $orderId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // --- دالة التعديل الجديدة (Edit) ---
    public function updateOrder(int $orderId, array $orderData, array $items): bool {
        try {
            $this->db->beginTransaction();

            // تحديث الجدول الرئيسي
            $sqlMain = "UPDATE {$this->table} 
                        SET customer_id = :customer_id, order_date = :order_date, 
                            total_amount = :total_amount, notes = :notes 
                        WHERE id = :id AND status = 'draft'"; // التعديل فقط للمسودات
            
            $this->db->query($sqlMain);
            $this->db->bind(':customer_id', $orderData['customer_id'], PDO::PARAM_INT);
            $this->db->bind(':order_date', $orderData['order_date']);
            $this->db->bind(':total_amount', $orderData['total_amount']);
            $this->db->bind(':notes', $orderData['notes']);
            $this->db->bind(':id', $orderId, PDO::PARAM_INT);
            $this->db->execute();

            // مسح الأصناف القديمة
            $this->db->query("DELETE FROM sales_order_items WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId, PDO::PARAM_INT);
            $this->db->execute();

            // إدراج الأصناف الجديدة
            $sqlItems = "INSERT INTO sales_order_items (order_id, product_id, quantity, price, subtotal) 
                         VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
            foreach ($items as $item) {
                $this->db->query($sqlItems);
                $this->db->bind(':order_id', $orderId, PDO::PARAM_INT);
                $this->db->bind(':product_id', $item['product_id'], PDO::PARAM_INT);
                $this->db->bind(':quantity', $item['quantity'], PDO::PARAM_INT);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteOrder(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND status = 'draft'");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}