<?php
// app/models/SalesOrder.php

class SalesOrder extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_orders';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. جدول أوامر البيع الأساسي
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'        => "INT DEFAULT 1",
            'order_number'      => "VARCHAR(50) NOT NULL",
            'customer_id'       => "INT NOT NULL",
            'order_date'        => "DATE NOT NULL",
            'expected_delivery' => "DATE NULL",
            'status'            => "VARCHAR(50) DEFAULT 'Draft'",
            'total_amount'      => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'             => "TEXT NULL",
            'created_by'        => "INT NOT NULL",
            'created_at'        => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // 2. جدول تفاصيل الأصناف المبيعة
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `sales_order_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $itemColumns = [
            'order_id'       => "INT NOT NULL", // 🟢 تم التعديل هنا ليطابق قاعدة بياناتك
            'product_id'     => "INT NOT NULL",
            'product_name'   => "VARCHAR(255) NOT NULL",
            'quantity'       => "DECIMAL(10,2) NOT NULL DEFAULT 1.00",
            'unit_price'     => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'total_price'    => "DECIMAL(15,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($itemColumns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `sales_order_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `sales_order_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllSalesOrders() {
        try {
            $sql = "SELECT so.*, c.name as customer_name, u.name as user_name 
                    FROM {$this->table} so 
                    LEFT JOIN customers c ON so.customer_id = c.id 
                    LEFT JOIN users u ON so.created_by = u.id 
                    WHERE so.company_id = :cid ORDER BY so.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getSalesOrderById($id) {
        try {
            $sql = "SELECT so.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email, c.address as customer_address, u.name as user_name 
                    FROM {$this->table} so 
                    LEFT JOIN customers c ON so.customer_id = c.id 
                    LEFT JOIN users u ON so.created_by = u.id 
                    WHERE so.id = :id AND so.company_id = :cid LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getSalesOrderItems($orderId) {
        try {
            // 🟢 تم التعديل هنا أيضاً ليجلب البيانات باستخدام order_id
            $this->db->query("SELECT * FROM sales_order_items WHERE order_id = :oid");
            $this->db->bind(':oid', $orderId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createSalesOrder($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, order_number, customer_id, order_date, expected_delivery, status, total_amount, notes, created_by) 
                    VALUES (:cid, :onum, :cid_fk, :odate, :edate, 'Draft', :total, :notes, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':onum', $data['order_number']);
            $this->db->bind(':cid_fk', $data['customer_id']);
            $this->db->bind(':odate', $data['order_date']);
            $this->db->bind(':edate', !empty($data['expected_delivery']) ? $data['expected_delivery'] : null);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $orderId = $this->db->lastInsertId();

            // 🟢 تم التعديل هنا للحفظ في حقل order_id
            $sqlItem = "INSERT INTO sales_order_items (order_id, product_id, product_name, quantity, unit_price, total_price) 
                        VALUES (:oid, :pid, :pname, :qty, :price, :ptotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':oid', $orderId);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':pname', $item['product_name']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':ptotal', $item['total_price']);
                $this->db->execute();
            }

            $this->db->commit();
            return $orderId;

        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage()); 
        }
    }

    public function deleteSalesOrder($id) {
        $this->db->beginTransaction();
        try {
            // 🟢 تم التعديل هنا للحذف باستخدام order_id
            $this->db->query("DELETE FROM sales_order_items WHERE order_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}