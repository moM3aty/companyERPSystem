<?php
// app/models/Purchase.php

class Purchase extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchases';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'   => "INT DEFAULT 1",
            'supplier_id'  => "INT NOT NULL",
            'order_number' => "VARCHAR(50) NOT NULL",
            'order_date'   => "DATE NOT NULL",
            'status'       => "VARCHAR(50) DEFAULT 'Draft'", 
            'total_amount' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'        => "TEXT NULL",
            'created_by'   => "INT NOT NULL",
            'created_at'   => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `purchase_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $itemColumns = [
            'purchase_id'  => "INT NOT NULL",
            'product_id'   => "INT NOT NULL", // 🟢 تمت إضافة الربط بالمخزون
            'product_name' => "VARCHAR(255) NOT NULL",
            'quantity'     => "DECIMAL(10,2) NOT NULL DEFAULT 1.00",
            'unit_price'   => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'total_price'  => "DECIMAL(15,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($itemColumns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `purchase_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `purchase_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllPurchases() {
        try {
            $sql = "SELECT p.*, s.company_name as supplier_name, u.name as user_name 
                    FROM {$this->table} p 
                    LEFT JOIN suppliers s ON p.supplier_id = s.id 
                    LEFT JOIN users u ON p.created_by = u.id 
                    WHERE p.company_id = :cid ORDER BY p.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getPurchaseById($id) {
        try {
            $sql = "SELECT p.*, s.company_name as supplier_name, s.email as supplier_email, s.phone as supplier_phone, u.name as user_name 
                    FROM {$this->table} p 
                    LEFT JOIN suppliers s ON p.supplier_id = s.id 
                    LEFT JOIN users u ON p.created_by = u.id 
                    WHERE p.id = :id AND p.company_id = :cid LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getPurchaseItems($purchaseId) {
        try {
            $this->db->query("SELECT * FROM purchase_items WHERE purchase_id = :pid");
            $this->db->bind(':pid', $purchaseId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createPurchase($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} (company_id, supplier_id, order_number, order_date, status, total_amount, notes, created_by)
                    VALUES (:cid, :sid, :onum, :odate, 'Draft', :total, :notes, :user)";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':sid', $data['supplier_id']);
            $this->db->bind(':onum', $data['order_number']);
            $this->db->bind(':odate', $data['order_date']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();

            $purchaseId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO purchase_items (purchase_id, product_id, product_name, quantity, unit_price, total_price)
                        VALUES (:pid, :prod_id, :pname, :qty, :price, :ptotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':pid', $purchaseId);
                $this->db->bind(':prod_id', $item['product_id']);
                $this->db->bind(':pname', $item['product_name']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['unit_price']);
                $this->db->bind(':ptotal', $item['total_price']);
                $this->db->execute();
            }

            $this->db->commit();
            return $purchaseId;
        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage());
        }
    }

    public function deletePurchase($id) {
        $this->db->beginTransaction();
        try {
            $this->db->query("DELETE FROM purchase_items WHERE purchase_id = :id");
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