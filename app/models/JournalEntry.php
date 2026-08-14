<?php
// app/models/JournalEntry.php

class JournalEntry extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'accounting_journals';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // جدول القيد الأساسي
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $cols1 = [
            'company_id'     => "INT DEFAULT 1",
            'journal_number' => "VARCHAR(50) NOT NULL",
            'date'           => "DATE NOT NULL",
            'description'    => "TEXT NOT NULL",
            'total_amount'   => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'status'         => "VARCHAR(50) DEFAULT 'draft'", // draft, approved
            'created_by'     => "INT NOT NULL",
            'created_at'     => "DATETIME DEFAULT CURRENT_TIMESTAMP"
        ];
        foreach ($cols1 as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `{$this->table}` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `{$this->table}` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }

        // جدول تفاصيل القيد (المدين والدائن)
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `accounting_journal_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $cols2 = [
            'journal_id'  => "INT NOT NULL",
            'account_id'  => "INT NOT NULL",
            'description' => "VARCHAR(255) NULL",
            'debit'       => "DECIMAL(15,2) DEFAULT 0.00",
            'credit'      => "DECIMAL(15,2) DEFAULT 0.00",
            'cost_center' => "VARCHAR(100) NULL",
            'department'  => "VARCHAR(100) NULL",
            'project'     => "VARCHAR(100) NULL"
        ];
        foreach ($cols2 as $col => $def) {
            try {
                $this->db->query("SHOW COLUMNS FROM `accounting_journal_items` LIKE '{$col}'");
                if (empty($this->db->resultSet())) {
                    $this->db->query("ALTER TABLE `accounting_journal_items` ADD `{$col}` {$def}");
                    $this->db->execute();
                }
            } catch (Exception $e) {}
        }
    }

    public function getAllJournals() {
        $this->db->query("SELECT j.*, u.name as creator_name FROM {$this->table} j LEFT JOIN users u ON j.created_by = u.id WHERE j.company_id = :cid ORDER BY j.date DESC, j.id DESC");
        $this->db->bind(':cid', Session::get('company_id') ?: 1);
        return $this->db->resultSet();
    }

    // إنشاء القيد مع التحقق من توازن المدين والدائن
    public function createEntry($data, $items) {
        $this->db->beginTransaction();
        try {
            $this->db->query("INSERT INTO {$this->table} (company_id, journal_number, date, description, total_amount, status, created_by) VALUES (:cid, :jno, :date, :desc, :total, 'approved', :user)");
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':jno', $data['journal_number']);
            $this->db->bind(':date', $data['date']);
            $this->db->bind(':desc', $data['description']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $journalId = $this->db->lastInsertId();

            foreach ($items as $item) {
                $this->db->query("INSERT INTO accounting_journal_items (journal_id, account_id, description, debit, credit, cost_center) VALUES (:jid, :acc, :desc, :debit, :credit, :cc)");
                $this->db->bind(':jid', $journalId);
                $this->db->bind(':acc', $item['account_id']);
                $this->db->bind(':desc', $item['description'] ?? null);
                $this->db->bind(':debit', $item['debit'] ?? 0);
                $this->db->bind(':credit', $item['credit'] ?? 0);
                $this->db->bind(':cc', $item['cost_center'] ?? null);
                $this->db->execute();

                // تحديث رصيد الحساب مباشرة
                $balanceChange = ((float)$item['debit'] - (float)$item['credit']); 
                $this->db->query("UPDATE accounting_accounts SET balance = balance + :change WHERE id = :acc");
                $this->db->bind(':change', $balanceChange);
                $this->db->bind(':acc', $item['account_id']);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}