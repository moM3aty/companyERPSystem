<?php
// app/models/SalesOrder.php

class SalesOrder extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_orders';
        $this->autoUpgradeTable();
    }

    /* STREAMING_CHUNK: Auto-upgrading tables... */
    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sales_orders` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'    => "INT DEFAULT 1",
            'order_number'  => "VARCHAR(50) NOT NULL DEFAULT 'SO-000'",
            'customer_id'   => "INT DEFAULT NULL",
            'customer_name' => "VARCHAR(255) DEFAULT NULL",
            'order_date'    => "DATE NULL",
            'total_amount'  => "DECIMAL(15,2) DEFAULT 0.00",
            'status'        => "VARCHAR(50) DEFAULT 'draft'",
            'notes'         => "TEXT DEFAULT NULL",
            'created_by'    => "INT NOT NULL DEFAULT 0",
            'created_at'    => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `sales_orders` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `sales_orders` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sales_order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL DEFAULT 1,
                `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
                `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    /* STREAMING_CHUNK: Fetching methods... */
    public function getAllOrders(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY created_at DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getOrderById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getOrderItems(int $orderId): array {
        $sql = "SELECT i.*, p.name as product_name, p.sku 
                FROM sales_order_items i 
                LEFT JOIN products p ON i.product_id = p.id 
                WHERE i.order_id = :oid";
        $this->db->query($sql);
        $this->db->bind(':oid', $orderId);
        return $this->db->resultSet();
    }

    /* STREAMING_CHUNK: Creating and Updating... */
    public function createOrder(array $data, array $items): bool|int {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table} 
                    (company_id, order_number, customer_id, customer_name, order_date, total_amount, status, notes, created_by) 
                    VALUES (:cid, :onum, :cid_fk, :cname, :odate, :total, :status, :notes, :user)";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':onum', $data['order_number']);
            $this->db->bind(':cid_fk', !empty($data['customer_id']) ? $data['customer_id'] : null);
            $this->db->bind(':cname', $data['customer_name']);
            $this->db->bind(':odate', $data['order_date']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();

            $orderId = (int)$this->db->lastInsertId();

            $sqlItem = "INSERT INTO sales_order_items (order_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (:oid, :pid, :qty, :price, :subtotal)";
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':oid', $orderId);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateOrder(int $id, array $data, array $items): bool {
        try {
            $this->db->beginTransaction();

            $sql = "UPDATE {$this->table} 
                    SET customer_id = :cid_fk, customer_name = :cname, order_date = :odate, 
                        total_amount = :total, status = :status, notes = :notes
                    WHERE id = :id AND company_id = :cid";
            
            $this->db->query($sql);
            $this->db->bind(':cid_fk', !empty($data['customer_id']) ? $data['customer_id'] : null);
            $this->db->bind(':cname', $data['customer_name']);
            $this->db->bind(':odate', $data['order_date']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            // مسح الأصناف القديمة
            $this->db->query("DELETE FROM sales_order_items WHERE order_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // إدخال الأصناف الجديدة
            $sqlItem = "INSERT INTO sales_order_items (order_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (:oid, :pid, :qty, :price, :subtotal)";
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':oid', $id);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':qty', $item['quantity']);
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
        try {
            $this->db->query("DELETE FROM sales_order_items WHERE order_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
        } catch(Exception $e) {}

        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}