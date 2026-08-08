<?php
// app/models/Stocktake.php

class Stocktake extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'stocktakes';
        
        // 🟢 الحل السحري: فحص وإنشاء جداول الجرد آلياً 🟢
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // 1. جدول عمليات الجرد الأساسية
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `stocktakes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) DEFAULT 1,
                `reference` varchar(50) NOT NULL,
                `stocktake_date` date NOT NULL,
                `status` varchar(50) DEFAULT 'draft',
                `notes` text DEFAULT NULL,
                `created_by` int(11) NOT NULL,
                `created_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            )";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. جدول أصناف الجرد
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `stocktake_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `stocktake_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `system_quantity` int(11) NOT NULL DEFAULT 0,
                `actual_quantity` int(11) NOT NULL DEFAULT 0,
                `variance` int(11) NOT NULL DEFAULT 0,
                `notes` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            )";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getAllStocktakes(): array {
        $sql = "SELECT s.*, u.name as creator_name 
                FROM {$this->table} s 
                LEFT JOIN users u ON s.created_by = u.id 
                WHERE s.company_id = :cid 
                ORDER BY s.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getStocktakeById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createStocktake(array $data): int|false {
        $sql = "INSERT INTO {$this->table} (company_id, reference, stocktake_date, status, notes, created_by) 
                VALUES (:cid, :ref, :sdate, :status, :notes, :created_by)";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':ref', $data['reference']);
        $this->db->bind(':sdate', $data['stocktake_date']);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':created_by', Session::getUserId());
        
        if ($this->db->execute()) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }

    public function updateStocktake(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET stocktake_date = :sdate, status = :status, notes = :notes 
                WHERE id = :id AND company_id = :cid";
        $this->db->query($sql);
        $this->db->bind(':sdate', $data['stocktake_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function deleteStocktake(int $id): bool {
        try {
            // مسح الأصناف المرتبطة أولاً لتجنب تراكم بيانات اليتيمة
            $this->db->query("DELETE FROM stocktake_items WHERE stocktake_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
        } catch (Exception $e) {}

        // مسح الجرد الأساسي
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }

    public function getItems(int $stocktakeId): array {
        $sql = "SELECT si.*, p.name as product_name, p.sku 
                FROM stocktake_items si 
                JOIN products p ON si.product_id = p.id 
                WHERE si.stocktake_id = :sid";
        $this->db->query($sql);
        $this->db->bind(':sid', $stocktakeId);
        return $this->db->resultSet();
    }

    public function addItem(array $data): bool {
        // التحقق من وجود الصنف مسبقاً في نفس الجرد
        $this->db->query("SELECT id FROM stocktake_items WHERE stocktake_id = :sid AND product_id = :pid");
        $this->db->bind(':sid', $data['stocktake_id']);
        $this->db->bind(':pid', $data['product_id']);
        if ($this->db->single()) return false;

        $sql = "INSERT INTO stocktake_items (stocktake_id, product_id, system_quantity, actual_quantity, variance, notes) 
                VALUES (:sid, :pid, :sys_qty, :act_qty, :var, :notes)";
        $this->db->query($sql);
        $this->db->bind(':sid', $data['stocktake_id']);
        $this->db->bind(':pid', $data['product_id']);
        $this->db->bind(':sys_qty', $data['system_quantity']);
        $this->db->bind(':act_qty', $data['actual_quantity']);
        $this->db->bind(':var', $data['variance']);
        $this->db->bind(':notes', $data['notes'] ?? null);
        return $this->db->execute();
    }

    public function removeItem(int $itemId): bool {
        $this->db->query("DELETE FROM stocktake_items WHERE id = :id");
        $this->db->bind(':id', $itemId);
        return $this->db->execute();
    }

    public function completeStocktake(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            // 1. تغيير حالة الجرد إلى مكتمل
            $this->db->query("UPDATE {$this->table} SET status = 'completed' WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // 2. جلب جميع الأصناف لتحديث أرصدتها
            $items = $this->getItems($id);
            $updateSql = "UPDATE products SET quantity = :qty WHERE id = :pid";
            
            foreach ($items as $item) {
                $this->db->query($updateSql);
                $this->db->bind(':qty', $item->actual_quantity);
                $this->db->bind(':pid', $item->product_id);
                $this->db->execute();
            }

            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('UPDATE', 'Stocktake', $id, "تم اعتماد عملية الجرد وتحديث أرصدة المنتجات.");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}