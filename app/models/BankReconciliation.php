<?php
// app/models/BankReconciliation.php

class BankReconciliation extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'bank_reconciliations';
        $this->autoUpgradeTable();
    }

    private function autoUpgradeTable() {
        // جدول التسويات البنكية
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}

        $columns = [
            'company_id'        => "INT DEFAULT 1",
            'treasury_id'       => "INT NOT NULL",
            'statement_date'    => "DATE NOT NULL",
            'system_balance'    => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'statement_balance' => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'difference'        => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'status'            => "VARCHAR(50) DEFAULT 'Draft'", // Draft, Reconciled
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

        // جدول تفاصيل العمليات التي تمت تسويتها
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS `bank_reconciliation_items` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `reconciliation_id` int(11) NOT NULL,
                `source_type` varchar(50) NOT NULL, /* Payment, Expense */
                `source_id` int(11) NOT NULL,
                `amount` decimal(15,2) NOT NULL,
                `transaction_type` varchar(10) NOT NULL, /* In, Out */
                PRIMARY KEY (`id`)
            )");
            $this->db->execute();
        } catch (Exception $e) {}
    }

    public function getAllReconciliations() {
        try {
            $sql = "SELECT r.*, t.name as bank_name, t.account_number, u.name as creator_name 
                    FROM {$this->table} r 
                    LEFT JOIN treasuries t ON r.treasury_id = t.id 
                    LEFT JOIN users u ON r.created_by = u.id 
                    WHERE r.company_id = :cid ORDER BY r.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getReconciliationById($id) {
        try {
            $this->db->query("SELECT r.*, t.name as bank_name, t.account_number, u.name as creator_name 
                              FROM {$this->table} r 
                              LEFT JOIN treasuries t ON r.treasury_id = t.id 
                              LEFT JOIN users u ON r.created_by = u.id 
                              WHERE r.id = :id AND r.company_id = :cid LIMIT 1");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    // جلب العمليات المعلقة (غير المسواة) لبنك معين حتى تاريخ محدد
    public function getUnclearedTransactions($treasuryId, $upToDate) {
        $transactions = [];

        // 🟢 ضمان وجود حقل is_reconciled قبل الاستعلام 🟢
        try {
            $this->db->query("SHOW COLUMNS FROM `payments` LIKE 'is_reconciled'");
            if (empty($this->db->resultSet())) {
                $this->db->query("ALTER TABLE `payments` ADD `is_reconciled` TINYINT(1) DEFAULT 0");
                $this->db->execute();
            }
        } catch (Exception $e) {}

        try {
            $this->db->query("SHOW COLUMNS FROM `expenses` LIKE 'is_reconciled'");
            if (empty($this->db->resultSet())) {
                $this->db->query("ALTER TABLE `expenses` ADD `is_reconciled` TINYINT(1) DEFAULT 0");
                $this->db->execute();
            }
        } catch (Exception $e) {}


        // 1. Payments (In / Out)
        try {
            $this->db->query("SELECT id, voucher_number as ref, payment_type as type, amount, payment_date as date, notes 
                              FROM payments 
                              WHERE treasury_id = :tid AND is_reconciled = 0 AND payment_date <= :dt");
            $this->db->bind(':tid', $treasuryId);
            $this->db->bind(':dt', $upToDate);
            $payments = $this->db->resultSet();
            foreach($payments as $p) {
                $transactions[] = [
                    'source' => 'Payment', 'id' => $p->id, 'ref' => $p->ref, 
                    'type' => $p->type, 'amount' => $p->amount, 'date' => $p->date, 'desc' => $p->notes
                ];
            }
        } catch (Exception $e) {}

        // 2. Expenses (Out)
        try {
            $this->db->query("SELECT id, reference as ref, amount, expense_date as date, notes 
                              FROM expenses 
                              WHERE treasury_id = :tid AND is_reconciled = 0 AND expense_date <= :dt");
            $this->db->bind(':tid', $treasuryId);
            $this->db->bind(':dt', $upToDate);
            $expenses = $this->db->resultSet();
            foreach($expenses as $e) {
                $transactions[] = [
                    'source' => 'Expense', 'id' => $e->id, 'ref' => $e->ref ?: 'EXP-'.$e->id, 
                    'type' => 'Out', 'amount' => $e->amount, 'date' => $e->date, 'desc' => $e->notes
                ];
            }
        } catch (Exception $e) {}

        // ترتيب حسب التاريخ
        usort($transactions, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        return $transactions;
    }

    public function saveReconciliation($data, $clearedItems) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} (company_id, treasury_id, statement_date, system_balance, statement_balance, difference, status, notes, created_by) 
                    VALUES (:cid, :tid, :sdate, :sysbal, :statbal, :diff, 'Reconciled', :notes, :user)";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->bind(':sdate', $data['statement_date']);
            $this->db->bind(':sysbal', $data['system_balance']);
            $this->db->bind(':statbal', $data['statement_balance']);
            $this->db->bind(':diff', $data['difference']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();

            $recId = $this->db->lastInsertId();

            $sqlItem = "INSERT INTO bank_reconciliation_items (reconciliation_id, source_type, source_id, amount, transaction_type) 
                        VALUES (:rid, :stype, :sid, :amt, :ttype)";

            foreach ($clearedItems as $item) {
                // حفظ التفاصيل
                $this->db->query($sqlItem);
                $this->db->bind(':rid', $recId);
                $this->db->bind(':stype', $item['source']);
                $this->db->bind(':sid', $item['id']);
                $this->db->bind(':amt', $item['amount']);
                $this->db->bind(':ttype', $item['type']);
                $this->db->execute();

                // تحديث حالة العملية إلى "مسواة"
                if ($item['source'] == 'Payment') {
                    $this->db->query("UPDATE payments SET is_reconciled = 1 WHERE id = :id");
                } else {
                    $this->db->query("UPDATE expenses SET is_reconciled = 1 WHERE id = :id");
                }
                $this->db->bind(':id', $item['id']);
                $this->db->execute();
            }

            $this->db->commit();
            return $recId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}