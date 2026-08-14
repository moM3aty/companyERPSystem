<?php
// app/models/PurchaseRequest.php

class PurchaseRequest extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_requests';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. جدول طلبات الشراء
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'      => "INT DEFAULT 1",
            'request_number'  => "VARCHAR(50) NOT NULL",
            'department'      => "VARCHAR(100) NULL",
            'request_date'    => "DATE NOT NULL",
            'status'          => "VARCHAR(50) DEFAULT 'Pending'", /* Pending, Approved, Rejected */
            'total_estimated' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'notes'           => "TEXT NULL",
            'created_by'      => "INT NOT NULL",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

        // 2. جدول أصناف طلب الشراء
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `purchase_request_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $itemColumns = [
            'request_id'      => "INT NOT NULL", // للربط مع الجدول الأساسي
            'product_name'    => "VARCHAR(255) NOT NULL", // اسم النص لأن الصنف قد لا يكون بالمخزن
            'quantity'        => "DECIMAL(10,2) NOT NULL DEFAULT 1.00",
            'estimated_price' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'total_price'     => "DECIMAL(15,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($itemColumns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `purchase_request_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `purchase_request_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllRequests() {
        try {
            $sql = "SELECT pr.*, u.name as user_name 
                    FROM {$this->table} pr 
                    LEFT JOIN users u ON pr.created_by = u.id 
                    WHERE pr.company_id = :cid ORDER BY pr.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getRequestById($id) {
        try {
            $sql = "SELECT pr.*, u.name as user_name 
                    FROM {$this->table} pr 
                    LEFT JOIN users u ON pr.created_by = u.id 
                    WHERE pr.id = :id AND pr.company_id = :cid LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function getRequestItems($reqId) {
        try {
            $this->db->query("SELECT * FROM purchase_request_items WHERE request_id = :rid");
            $this->db->bind(':rid', $reqId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function createRequest($data, $items) {
        $this->db->beginTransaction();
        try {
            // حفظ الطلب الأساسي
            $sql = "INSERT INTO {$this->table} 
                    (company_id, request_number, department, request_date, status, total_estimated, notes, created_by) 
                    VALUES (:cid, :rnum, :dept, :rdate, 'Pending', :total, :notes, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':rnum', $data['request_number']);
            $this->db->bind(':dept', $data['department']);
            $this->db->bind(':rdate', $data['request_date']);
            $this->db->bind(':total', $data['total_estimated']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $reqId = $this->db->lastInsertId();

            // حفظ الأصناف
            $sqlItem = "INSERT INTO purchase_request_items (request_id, product_name, quantity, estimated_price, total_price) 
                        VALUES (:rid, :pname, :qty, :price, :ptotal)";
            
            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':rid', $reqId);
                $this->db->bind(':pname', $item['product_name']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':price', $item['estimated_price']);
                $this->db->bind(':ptotal', $item['total_price']);
                $this->db->execute();
            }

            $this->db->commit();
            return $reqId;

        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage()); 
        }
    }

    public function deleteRequest($id) {
        $this->db->beginTransaction();
        try {
            $this->db->query("DELETE FROM purchase_request_items WHERE request_id = :id");
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