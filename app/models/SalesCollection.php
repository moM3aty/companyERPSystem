<?php
// app/models/SalesCollection.php

class SalesCollection extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'sales_collections';
        $this->autoUpgradeTable();
    }

    /* STREAMING_CHUNK: Auto-upgrading tables... */
    private function autoUpgradeTable() {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'      => "INT DEFAULT 1",
            'receipt_number'  => "VARCHAR(50) NOT NULL",
            'invoice_id'      => "INT NOT NULL",
            'treasury_id'     => "INT NOT NULL",
            'amount'          => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'collection_date' => "DATE NOT NULL",
            'payment_method'  => "VARCHAR(50) DEFAULT 'cash'",
            'reference'       => "VARCHAR(255) DEFAULT NULL",
            'notes'           => "TEXT DEFAULT NULL",
            'created_by'      => "INT NOT NULL DEFAULT 0",
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
    }

    /* STREAMING_CHUNK: Fetching collections... */
    public function getAllCollections(): array {
        $sql = "SELECT c.*, i.invoice_number, t.name as treasury_name, u.name as creator_name 
                FROM {$this->table} c
                LEFT JOIN invoices i ON c.invoice_id = i.id
                LEFT JOIN treasuries t ON c.treasury_id = t.id
                LEFT JOIN users u ON c.created_by = u.id
                WHERE c.company_id = :cid
                ORDER BY c.collection_date DESC, c.id DESC";
                
        $this->db->query($sql);
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    /* STREAMING_CHUNK: Adding collection and updating treasury balance... */
    public function addCollection(array $data): bool {
        try {
            $this->db->beginTransaction();

            // 1. تسجيل إيصال التحصيل
            $sql = "INSERT INTO {$this->table} 
                    (company_id, receipt_number, invoice_id, treasury_id, amount, collection_date, payment_method, reference, notes, created_by) 
                    VALUES (:cid, :rnum, :iid, :tid, :amt, :cdate, :method, :ref, :notes, :user)";
                    
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':rnum', $data['receipt_number']);
            $this->db->bind(':iid', $data['invoice_id']);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':cdate', $data['collection_date']);
            $this->db->bind(':method', $data['payment_method']);
            $this->db->bind(':ref', $data['reference'] ?? null);
            $this->db->bind(':notes', $data['notes'] ?? null);
            $this->db->bind(':user', $data['created_by']);
            $this->db->execute();

            $collectionId = $this->db->lastInsertId();

            // 2. تحديث رصيد الخزنة المُختارة
            $this->db->query("UPDATE treasuries SET balance = balance + :amt WHERE id = :tid");
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->execute();

            // 3. تسجيل الحدث في سجل النشاطات (إن وُجد)
            if (class_exists('ActivityLog')) {
                ActivityLog::logAction('COLLECT', 'Sales', $collectionId, "تم تحصيل مبلغ {$data['amount']} لفاتورة {$data['invoice_id']}");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}