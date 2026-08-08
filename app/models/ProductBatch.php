<?php
// app/models/ProductBatch.php

class ProductBatch extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'product_batches';
        
        $this->autoUpgradeTable();
    }

    /**
     * دالة ذكية لفحص وإنشاء الأعمدة الناقصة لجدول التشغيلات والسيريالات
     */
    private function autoUpgradeTable() {
        // 1. التأكد من وجود الجدول أصلاً وإنشاؤه إن لم يكن موجوداً
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )";
            $this->db->query($sql);
            $this->db->execute();
        } catch (Exception $e) {}

        // 2. إضافة كافة الأعمدة المحتمل نقصها واحداً تلو الآخر لتجنب الأخطاء
        $columnsToAdd = [
            'company_id'      => "INT DEFAULT 1",
            'product_id'      => "INT NOT NULL DEFAULT 0",
            'lot_number'      => "VARCHAR(100) NULL",
            'batch_number'    => "VARCHAR(100) NULL",
            'serial_number'   => "VARCHAR(100) NULL",
            'production_date' => "DATE NULL",
            'expiry_date'     => "DATE NULL",
            'quantity'        => "INT DEFAULT 0",
            'notes'           => "TEXT NULL",
            'status'          => "VARCHAR(50) DEFAULT 'active'",
            'created_at'      => "DATETIME DEFAULT CURRENT_TIMESTAMP"
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
                // تجاهل بصمت حتى لا يتوقف النظام
            }
        }
    }

    public function getBatchesByProduct(int $productId): array {
        $sql = "SELECT b.*, p.name as product_name 
                FROM {$this->table} b 
                LEFT JOIN products p ON b.product_id = p.id 
                WHERE b.product_id = :pid AND b.company_id = :cid 
                ORDER BY b.expiry_date ASC, b.id DESC";
        $this->db->query($sql);
        $this->db->bind(':pid', $productId);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getAllBatches(): array {
        $sql = "SELECT b.*, p.name as product_name 
                FROM {$this->table} b 
                LEFT JOIN products p ON b.product_id = p.id 
                WHERE b.company_id = :cid 
                ORDER BY b.id DESC";
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    public function getBatchById(int $id): ?object {
        $this->db->query("SELECT * FROM {$this->table} WHERE id = :id AND company_id = :cid LIMIT 1");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->single();
    }

    public function isSerialNumberUnique(string $serial, ?int $excludeId = null): bool {
        if (empty($serial)) return true; // السيريال الفارغ مسموح به (إذا كانت تشغيلة كميات)
        
        $sql = "SELECT id FROM {$this->table} WHERE serial_number = :serial AND company_id = :cid";
        if ($excludeId) {
            $sql .= " AND id != :excludeId";
        }
        $this->db->query($sql);
        $this->db->bind(':serial', $serial);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        if ($excludeId) {
            $this->db->bind(':excludeId', $excludeId);
        }
        $this->db->execute();
        
        return $this->db->rowCount() === 0;
    }

    public function addBatch(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (company_id, product_id, lot_number, batch_number, serial_number, production_date, expiry_date, quantity, notes, status) 
                VALUES (:cid, :pid, :lot, :batch, :serial, :pdate, :edate, :qty, :notes, :status)";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        $this->db->bind(':pid', $data['product_id']);
        $this->db->bind(':lot', $data['lot_number'] ?? null);
        $this->db->bind(':batch', $data['batch_number'] ?? null); // قديم للتوافق
        $this->db->bind(':serial', $data['serial_number'] ?? null);
        $this->db->bind(':pdate', !empty($data['production_date']) ? $data['production_date'] : null);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':qty', $data['quantity'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'active');
        
        return $this->db->execute();
    }

    public function updateBatch(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} 
                SET lot_number = :lot, batch_number = :batch, serial_number = :serial, 
                    production_date = :pdate, expiry_date = :edate, quantity = :qty, 
                    notes = :notes, status = :status
                WHERE id = :id AND company_id = :cid";
                
        $this->db->query($sql);
        $this->db->bind(':lot', $data['lot_number'] ?? null);
        $this->db->bind(':batch', $data['batch_number'] ?? null);
        $this->db->bind(':serial', $data['serial_number'] ?? null);
        $this->db->bind(':pdate', !empty($data['production_date']) ? $data['production_date'] : null);
        $this->db->bind(':edate', !empty($data['expiry_date']) ? $data['expiry_date'] : null);
        $this->db->bind(':qty', $data['quantity'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'active');
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        
        return $this->db->execute();
    }

    public function deleteBatch(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $id);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->execute();
    }
}