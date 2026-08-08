<?php
// app/models/Warehouse.php

class Warehouse extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'warehouses';
        
        // 🟢 الحل السحري: فحص وتحديث جداول المستودعات والنقل آلياً 🟢
        $this->autoUpgradeTable();
    }

    /**
     * دالة ذكية لفحص وإضافة الأعمدة الناقصة (مثل company_id) لتجنب انهيار النظام
     */
    private function autoUpgradeTable() {
        // 1. ترقية جدول المستودعات
        $columnsToAdd = [
            'company_id' => "INT DEFAULT 1 AFTER `id`",
            'code'       => "VARCHAR(50) NULL AFTER `name`",
            'address'    => "TEXT NULL AFTER `code`",
            'is_main'    => "TINYINT(1) DEFAULT 0 AFTER `address`"
        ];

        foreach ($columnsToAdd as $colName => $colDef) {
            try {
                $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE '{$colName}'");
                $exists = $this->db->resultSet();
                
                if (empty($exists)) {
                    $this->db->query("ALTER TABLE {$this->table} ADD `{$colName}` {$colDef}");
                    $this->db->execute();
                }
            } catch (Exception $e) {
                // تجاهل الأخطاء بصمت كي لا يتوقف النظام
            }
        }

        // 2. إنشاء وتجهيز جدول تحويلات المخزون (stock_transfers) إذا لم يكن موجوداً
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `stock_transfers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `company_id` int(11) DEFAULT 1,
                `transfer_number` varchar(50) NOT NULL,
                `from_warehouse_id` int(11) NOT NULL,
                `to_warehouse_id` int(11) NOT NULL,
                `product_id` int(11) NOT NULL,
                `quantity` int(11) NOT NULL,
                `requested_by` int(11) NOT NULL,
                `notes` text DEFAULT NULL,
                `status` varchar(50) DEFAULT 'completed',
                `created_at` datetime DEFAULT current_timestamp(),
                PRIMARY KEY (`id`)
            )";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Exception $e) {
            // تجاهل بصمت
        }
    }

    public function getAllWarehouses(): array {
        $this->db->query("SELECT * FROM {$this->table} WHERE company_id = :cid ORDER BY is_main DESC, id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getWarehouseById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function createWarehouse(array $data): bool {
        $companyId = Session::get('company_id') ?: 1;

        $sql = "INSERT INTO {$this->table} (company_id, name, code, address, is_main, created_at) 
                VALUES (:cid, :name, :code, :address, :is_main, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':is_main', $data['is_main'] ?? 0);
        
        return $this->db->execute();
    }

    public function updateWarehouse(int $id, array $data): bool {
        $companyId = Session::get('company_id') ?: 1;

        $sql = "UPDATE {$this->table} 
                SET name = :name, code = :code, address = :address, is_main = :is_main 
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':is_main', $data['is_main'] ?? 0);
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', $companyId);
        
        return $this->db->execute();
    }

    public function deleteWarehouse(int $id): bool {
        $companyId = Session::get('company_id') ?: 1;

        // نمنع حذف المستودع الرئيسي كإجراء أمان (يجب أن يعين مستودع آخر كرئيسي أولاً)
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND is_main = 0 AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', $companyId);
        
        return $this->db->execute();
    }

    public function transferStock(int $fromWh, int $toWh, int $productId, int $quantity, int $userId, string $notes): string {
        $companyId = Session::get('company_id') ?: 1;
        
        // إنشاء رقم مرجعي مميز لأمر النقل
        $transferNumber = 'TR-' . date('Ymd') . '-' . rand(100, 999);
        
        $sql = "INSERT INTO stock_transfers 
                (company_id, transfer_number, from_warehouse_id, to_warehouse_id, product_id, quantity, requested_by, notes, status, created_at) 
                VALUES (:cid, :transfer_number, :fromWh, :toWh, :productId, :quantity, :userId, :notes, 'completed', NOW())";
                
        $this->db->query($sql);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':transfer_number', $transferNumber);
        $this->db->bind(':fromWh', $fromWh);
        $this->db->bind(':toWh', $toWh);
        $this->db->bind(':productId', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':userId', $userId);
        $this->db->bind(':notes', $notes);
        
        if ($this->db->execute()) {
            // تسجيل الحركة في سجل النشاطات (Activity Log)
            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('TRANSFER', 'Warehouses', $this->db->lastInsertId(), "تم نقل مخزون للمنتج ID {$productId} برقم مرجعي {$transferNumber}");
            }
            return $transferNumber;
        }
        
        throw new Exception("فشل في تسجيل أمر النقل بقاعدة البيانات.");
    }
}