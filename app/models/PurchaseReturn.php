<?php
// app/models/PurchaseReturn.php

class PurchaseReturn extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'purchase_returns';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. جدول المرتجعات الأساسي
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `purchase_returns` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'    => "INT DEFAULT 1",
            'return_number' => "VARCHAR(50) NOT NULL DEFAULT 'PRT-000'",
            'supplier_id'   => "INT DEFAULT NULL",
            'supplier_name' => "VARCHAR(255) DEFAULT NULL",
            'return_date'   => "DATE NULL",
            'total_amount'  => "DECIMAL(15,2) DEFAULT 0.00",
            'status'        => "VARCHAR(50) DEFAULT 'approved'",
            'notes'         => "TEXT DEFAULT NULL",
            'created_by'    => "INT NOT NULL DEFAULT 0",
            'created_at'    => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];

        foreach ($columns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `purchase_returns` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `purchase_returns` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // إجبار نوع عمود الحالة لتفادي الأخطاء
        try {
            $this->db->query("ALTER TABLE `purchase_returns` MODIFY COLUMN `status` VARCHAR(50) DEFAULT 'approved'");
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. جدول أصناف المرتجعات
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `purchase_return_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $itemColumns = [
            'return_id'  => "INT NOT NULL",
            'product_id' => "INT NOT NULL",
            'quantity'   => "DECIMAL(10,2) NOT NULL DEFAULT 1.00",
            'unit_cost'  => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'subtotal'   => "DECIMAL(15,2) NOT NULL DEFAULT 0.00"
        ];

        foreach ($itemColumns as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `purchase_return_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `purchase_return_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllReturns(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY created_at DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getReturnById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function getReturnItems(int $returnId): array {
        $sql = "SELECT i.*, p.name as product_name, p.sku 
                FROM purchase_return_items i 
                LEFT JOIN products p ON i.product_id = p.id 
                WHERE i.return_id = :rid";
        $this->db->query($sql);
        $this->db->bind(':rid', $returnId);
        return $this->db->resultSet();
    }

    public function createReturn(array $data, array $items): int|bool {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table} 
                    (company_id, return_number, supplier_id, supplier_name, return_date, total_amount, status, notes, created_by) 
                    VALUES (:cid, :rnum, :sid, :sname, :rdate, :total, :status, :notes, :user)";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':rnum', $data['return_number']);
            $this->db->bind(':sid', !empty($data['supplier_id']) ? $data['supplier_id'] : null);
            $this->db->bind(':sname', $data['supplier_name']);
            $this->db->bind(':rdate', $data['return_date']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();

            $returnId = (int)$this->db->lastInsertId();

            $sqlItem = "INSERT INTO purchase_return_items (return_id, product_id, quantity, unit_cost, subtotal) 
                        VALUES (:rid, :pid, :qty, :cost, :subtotal)";
            
            $sqlStock = "UPDATE products SET quantity = quantity - :qty WHERE id = :pid";

            foreach ($items as $item) {
                // إدراج الصنف
                $this->db->query($sqlItem);
                $this->db->bind(':rid', $returnId);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':cost', $item['cost']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                // 🟢 خصم الكمية من المخزون (لأنها مرتجع للمورد)
                if ($data['status'] === 'approved') {
                    $this->db->query($sqlStock);
                    $this->db->bind(':qty', $item['quantity']);
                    $this->db->bind(':pid', $item['product_id']);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return $returnId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateReturn(int $id, array $data, array $items): bool {
        try {
            $this->db->beginTransaction();

            // إرجاع المخزون القديم أولاً (استرداد)
            $oldReturn = $this->getReturnById($id);
            if ($oldReturn && $oldReturn->status === 'approved') {
                $oldItems = $this->getReturnItems($id);
                $sqlRevert = "UPDATE products SET quantity = quantity + :qty WHERE id = :pid";
                foreach($oldItems as $old) {
                    $this->db->query($sqlRevert);
                    $this->db->bind(':qty', $old->quantity);
                    $this->db->bind(':pid', $old->product_id);
                    $this->db->execute();
                }
            }

            // تحديث البيانات الأساسية
            $sql = "UPDATE {$this->table} 
                    SET supplier_id = :sid, supplier_name = :sname, return_date = :rdate, 
                        total_amount = :total, status = :status, notes = :notes
                    WHERE id = :id AND company_id = :cid";
            
            $this->db->query($sql);
            $this->db->bind(':sid', !empty($data['supplier_id']) ? $data['supplier_id'] : null);
            $this->db->bind(':sname', $data['supplier_name']);
            $this->db->bind(':rdate', $data['return_date']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            // مسح الأصناف القديمة
            $this->db->query("DELETE FROM purchase_return_items WHERE return_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // إدخال الأصناف الجديدة وخصم المخزون
            $sqlItem = "INSERT INTO purchase_return_items (return_id, product_id, quantity, unit_cost, subtotal) 
                        VALUES (:rid, :pid, :qty, :cost, :subtotal)";
            $sqlDeduct = "UPDATE products SET quantity = quantity - :qty WHERE id = :pid";

            foreach ($items as $item) {
                $this->db->query($sqlItem);
                $this->db->bind(':rid', $id);
                $this->db->bind(':pid', $item['product_id']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':cost', $item['cost']);
                $this->db->bind(':subtotal', $item['subtotal']);
                $this->db->execute();

                if ($data['status'] === 'approved') {
                    $this->db->query($sqlDeduct);
                    $this->db->bind(':qty', $item['quantity']);
                    $this->db->bind(':pid', $item['product_id']);
                    $this->db->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteReturn(int $id): bool {
        try {
            $this->db->beginTransaction();

            $oldReturn = $this->getReturnById($id);
            if ($oldReturn && $oldReturn->status === 'approved') {
                $oldItems = $this->getReturnItems($id);
                $sqlRevert = "UPDATE products SET quantity = quantity + :qty WHERE id = :pid";
                foreach($oldItems as $old) {
                    $this->db->query($sqlRevert);
                    $this->db->bind(':qty', $old->quantity);
                    $this->db->bind(':pid', $old->product_id);
                    $this->db->execute();
                }
            }

            $this->db->query("DELETE FROM purchase_return_items WHERE return_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch(Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}