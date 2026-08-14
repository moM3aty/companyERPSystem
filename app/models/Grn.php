<?php
// app/models/Grn.php

class Grn extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'goods_received_notes';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // Main GRN table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'    => "INT DEFAULT 1",
            'grn_number'    => "VARCHAR(50) NOT NULL",
            'po_id'         => "INT NULL",
            'supplier_id'   => "INT NOT NULL",
            'warehouse_id'  => "INT NOT NULL",
            'delivery_date' => "DATE NOT NULL",
            'received_by'   => "INT NOT NULL",
            'delivery_note' => "VARCHAR(100) NULL", // Supplier's delivery note number
            'status'        => "VARCHAR(50) DEFAULT 'Received'", // Received, Verified, Invoiced
            'notes'         => "TEXT NULL",
            'attachment'    => "VARCHAR(255) NULL",
            'created_at'    => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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

        // GRN Items table
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `grn_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `grn_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `ordered_qty` decimal(10,2) DEFAULT 0.00,
                `received_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
                `damaged_qty` decimal(10,2) DEFAULT 0.00,
                `accepted_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
                `batch_number` varchar(100) NULL,
                `serial_number` varchar(100) NULL,
                `expiry_date` date NULL,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getAllGrns() {
        $sql = "SELECT g.*, s.company_name as supplier_name, w.name as warehouse_name, po.po_number 
                FROM {$this->table} g 
                LEFT JOIN suppliers s ON g.supplier_id = s.id 
                LEFT JOIN warehouses w ON g.warehouse_id = w.id 
                LEFT JOIN purchase_orders po ON g.po_id = po.id
                WHERE g.company_id = :cid ORDER BY g.created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getGrnById($id) {
        $this->db->query("SELECT g.*, s.company_name as supplier_name, w.name as warehouse_name, u.name as receiver_name, po.po_number 
                          FROM {$this->table} g 
                          LEFT JOIN suppliers s ON g.supplier_id = s.id 
                          LEFT JOIN warehouses w ON g.warehouse_id = w.id 
                          LEFT JOIN users u ON g.received_by = u.id 
                          LEFT JOIN purchase_orders po ON g.po_id = po.id
                          WHERE g.id = :id AND g.company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getGrnItems($grnId) {
        $this->db->query("SELECT gi.*, p.name as product_name, p.sku 
                          FROM grn_items gi 
                          LEFT JOIN products p ON gi.product_id = p.id 
                          WHERE gi.grn_id = :gid");
        $this->db->bind(':gid', $grnId);
        return $this->db->resultSet();
    }

    public function createGrn($data, $items) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} 
                    (company_id, grn_number, po_id, supplier_id, warehouse_id, delivery_date, received_by, delivery_note, notes, attachment) 
                    VALUES (:cid, :grn_num, :po_id, :supp_id, :wh_id, :del_date, :rec_by, :dnote, :notes, :attach)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':grn_num', $data['grn_number']);
            $this->db->bind(':po_id', !empty($data['po_id']) ? $data['po_id'] : null);
            $this->db->bind(':supp_id', $data['supplier_id']);
            $this->db->bind(':wh_id', $data['warehouse_id']);
            $this->db->bind(':del_date', $data['delivery_date']);
            $this->db->bind(':rec_by', Session::getUserId());
            $this->db->bind(':dnote', $data['delivery_note']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':attach', $data['attachment']);
            $this->db->execute();
            
            $grnId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO grn_items (grn_id, product_id, ordered_qty, received_qty, damaged_qty, accepted_qty, batch_number, serial_number, expiry_date) 
                        VALUES (:gid, :prod_id, :ord_qty, :rec_qty, :dam_qty, :acc_qty, :batch, :serial, :exp_date)";
            
            // لتحديث مخزون المستودع والمنتج
            $updateProductQtySql = "UPDATE products SET quantity = quantity + :acc_qty WHERE id = :prod_id";
            
            foreach ($items as $item) {
                if(empty($item['product_id'])) continue; // تجاهل الخدمات

                $this->db->query($sqlItem);
                $this->db->bind(':gid', $grnId);
                $this->db->bind(':prod_id', $item['product_id']);
                $this->db->bind(':ord_qty', $item['ordered_qty']);
                $this->db->bind(':rec_qty', $item['received_qty']);
                $this->db->bind(':dam_qty', $item['damaged_qty']);
                $this->db->bind(':acc_qty', $item['accepted_qty']);
                $this->db->bind(':batch', !empty($item['batch_number']) ? $item['batch_number'] : null);
                $this->db->bind(':serial', !empty($item['serial_number']) ? $item['serial_number'] : null);
                $this->db->bind(':exp_date', !empty($item['expiry_date']) ? $item['expiry_date'] : null);
                $this->db->execute();

                // 🟢 تحديث أرصدة المنتجات فورياً بالكمية المقبولة (Accepted) 🟢
                $this->db->query($updateProductQtySql);
                $this->db->bind(':acc_qty', $item['accepted_qty']);
                $this->db->bind(':prod_id', $item['product_id']);
                $this->db->execute();
                
                // 🟢 إذا كان المنتج يتعقب التشغيلات (Track Batches)، يتم إنشاء تشغيلة آلياً
                if (!empty($item['batch_number']) || !empty($item['serial_number']) || !empty($item['expiry_date'])) {
                    $batchSql = "INSERT INTO product_batches (company_id, product_id, lot_number, serial_number, expiry_date, quantity, status) 
                                 VALUES (:cid, :pid, :lot, :serial, :edate, :qty, 'active')";
                    $this->db->query($batchSql);
                    $this->db->bind(':cid', Session::get('company_id') ?: 1);
                    $this->db->bind(':pid', $item['product_id']);
                    $this->db->bind(':lot', !empty($item['batch_number']) ? $item['batch_number'] : null);
                    $this->db->bind(':serial', !empty($item['serial_number']) ? $item['serial_number'] : null);
                    $this->db->bind(':edate', !empty($item['expiry_date']) ? $item['expiry_date'] : null);
                    $this->db->bind(':qty', $item['accepted_qty']);
                    $this->db->execute();
                }
            }

            // تحديث حالة الـ PO إن وجد (Partially Received أو Completed بناء على المقارنة - هنا نبسطها كـ Completed)
            if (!empty($data['po_id'])) {
                $this->db->query("UPDATE purchase_orders SET status = 'Completed' WHERE id = :poid");
                $this->db->bind(':poid', $data['po_id']);
                $this->db->execute();
            }

            $this->db->commit();
            return $grnId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteGrn($id) {
        // الحذف المعقد الذي يعكس المخزون، نتركه للحماية، ونحذف السجلات فقط (أو نمنع الحذف إذا كان مرتبط بفاتورة)
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}