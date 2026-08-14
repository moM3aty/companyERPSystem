<?php
// app/models/Payment.php

class Payment extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payments';
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
            'company_id'      => "INT DEFAULT 1",
            'voucher_number'  => "VARCHAR(50) NOT NULL",
            'payment_type'    => "VARCHAR(20) DEFAULT 'Out'", 
            'treasury_id'     => "INT NOT NULL",
            'supplier_id'     => "INT NULL",
            'customer_id'     => "INT NULL",
            'invoice_id'      => "INT NULL",
            'payment_date'    => "DATE NOT NULL",
            'amount'          => "DECIMAL(15,2) NOT NULL DEFAULT 0.00",
            'payment_method'  => "VARCHAR(50) DEFAULT 'Cash'",
            'reference_number'=> "VARCHAR(100) NULL",
            'notes'           => "TEXT NULL",
            'attachment'      => "VARCHAR(255) NULL",
            'is_reconciled'   => "TINYINT(1) DEFAULT 0",
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
    }

    public function getAllPayments() {
        try {
            $sql = "SELECT p.*, t.name as treasury_name, s.company_name as supplier_name, c.name as customer_name, u.name as user_name 
                    FROM {$this->table} p 
                    LEFT JOIN treasuries t ON p.treasury_id = t.id 
                    LEFT JOIN suppliers s ON p.supplier_id = s.id 
                    LEFT JOIN customers c ON p.customer_id = c.id 
                    LEFT JOIN users u ON p.created_by = u.id 
                    WHERE p.company_id = :cid ORDER BY p.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getPaymentById($id) {
        try {
            $sql = "SELECT p.*, t.name as treasury_name, s.company_name as supplier_name, c.name as customer_name, u.name as user_name 
                    FROM {$this->table} p 
                    LEFT JOIN treasuries t ON p.treasury_id = t.id 
                    LEFT JOIN suppliers s ON p.supplier_id = s.id 
                    LEFT JOIN customers c ON p.customer_id = c.id 
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

    public function createPayment($data) {
        $this->db->beginTransaction();
        try {
            // 1. إنشاء السند
            $sql = "INSERT INTO {$this->table} 
                    (company_id, voucher_number, payment_type, treasury_id, supplier_id, customer_id, invoice_id, 
                     payment_date, amount, payment_method, reference_number, notes, attachment, created_by) 
                    VALUES (:cid, :vnum, :type, :tid, :sid, :cust_id, :inv_id, :pdate, :amt, :pmethod, :ref, :notes, :attach, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':vnum', $data['voucher_number']);
            $this->db->bind(':type', $data['payment_type']);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->bind(':sid', !empty($data['supplier_id']) ? $data['supplier_id'] : null);
            $this->db->bind(':cust_id', !empty($data['customer_id']) ? $data['customer_id'] : null);
            $this->db->bind(':inv_id', !empty($data['invoice_id']) ? $data['invoice_id'] : null);
            $this->db->bind(':pdate', $data['payment_date']);
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':pmethod', $data['payment_method']);
            $this->db->bind(':ref', $data['reference_number']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':attach', $data['attachment']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $paymentId = $this->db->lastInsertId();

            // 2. 🟢 تحديث رصيد الخزنة الإجباري في الحقل المعروض في الشاشة (current_balance) 🟢
            $operator = $data['payment_type'] == 'Out' ? '-' : '+';
            $this->db->query("UPDATE treasuries SET current_balance = current_balance {$operator} :amt WHERE id = :tid");
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->execute();

            // 3. تحديث مديونية العميل أو المورد
            if ($data['payment_type'] == 'Out' && !empty($data['supplier_id'])) {
                try {
                    $this->db->query("UPDATE suppliers SET balance = balance - :amt WHERE id = :sid");
                    $this->db->bind(':amt', $data['amount']);
                    $this->db->bind(':sid', $data['supplier_id']);
                    $this->db->execute();
                } catch(Exception $e) {
                    $this->db->query("UPDATE suppliers SET current_balance = current_balance - :amt WHERE id = :sid");
                    $this->db->bind(':amt', $data['amount']);
                    $this->db->bind(':sid', $data['supplier_id']);
                    $this->db->execute();
                }
            } elseif ($data['payment_type'] == 'In' && !empty($data['customer_id'])) {
                try {
                    $this->db->query("UPDATE customers SET balance = balance - :amt WHERE id = :cid");
                    $this->db->bind(':amt', $data['amount']);
                    $this->db->bind(':cid', $data['customer_id']);
                    $this->db->execute();
                } catch(Exception $e) {
                    $this->db->query("UPDATE customers SET current_balance = current_balance - :amt WHERE id = :cid");
                    $this->db->bind(':amt', $data['amount']);
                    $this->db->bind(':cid', $data['customer_id']);
                    $this->db->execute();
                }
            }

            // 4. تأكيد الحفظ
            $this->db->commit();

            // إنشاء القيد المحاسبي
            try {
                $this->createPaymentJournal($data, $paymentId);
            } catch (Throwable $th) {}

            return $paymentId;

        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage());
        }
    }

    private function createPaymentJournal($data, $paymentId) {
        if (!file_exists('../app/models/JournalEntry.php')) return;
        require_once '../app/models/JournalEntry.php';
        if (!class_exists('JournalEntry')) return;

        $jeModel = new JournalEntry();
        
        $this->db->query("SELECT chart_account_id FROM treasuries WHERE id = :tid");
        $this->db->bind(':tid', $data['treasury_id']);
        $treasuryAcc = $this->db->single();
        $cashAccId = $treasuryAcc ? $treasuryAcc->chart_account_id : null;

        $partyAccId = null;
        $table = "chart_of_accounts";
        try {
            $this->db->query("SELECT 1 FROM `accounting_accounts` LIMIT 1");
            $table = "accounting_accounts";
        } catch(Exception $e) {}

        if ($data['payment_type'] == 'Out') {
            $this->db->query("SELECT id FROM {$table} WHERE type = 'Liability' OR account_type = 'Liability' LIMIT 1");
        } else {
            $this->db->query("SELECT id FROM {$table} WHERE type = 'Asset' OR account_type = 'Asset' LIMIT 1");
        }
        
        $party = $this->db->single();
        if ($party) $partyAccId = $party->id;

        if ($cashAccId && $partyAccId) {
            $jeData = [
                'journal_number' => 'JV-' . $data['payment_type'] . '-' . time(),
                'date' => $data['payment_date'],
                'description' => "سند " . ($data['payment_type'] == 'Out' ? "صرف" : "قبض") . " رقم: {$data['voucher_number']}",
                'total_amount' => $data['amount']
            ];

            $lines = [];
            if ($data['payment_type'] == 'Out') {
                $lines[] = ['account_id' => $partyAccId, 'description' => "سداد للمورد", 'debit' => $data['amount'], 'credit' => 0];
                $lines[] = ['account_id' => $cashAccId, 'description' => "خروج نقدية", 'debit' => 0, 'credit' => $data['amount']];
            } else {
                $lines[] = ['account_id' => $cashAccId, 'description' => "دخول نقدية", 'debit' => $data['amount'], 'credit' => 0];
                $lines[] = ['account_id' => $partyAccId, 'description' => "تحصيل من عميل", 'debit' => 0, 'credit' => $data['amount']];
            }
            $jeModel->createEntry($jeData, $lines);
        }
        
    }
    public function deletePayment($id) {
        $this->db->beginTransaction();
        try {
            $payment = $this->getPaymentById($id);
            if (!$payment) throw new Exception("السند غير موجود");

            // 1. عكس رصيد الخزنة (إذا كان صرف نرجعه بالجمع، وإذا كان قبض نخصمه بالطرح)
            $operator = $payment->payment_type == 'Out' ? '+' : '-';
            $this->db->query("UPDATE treasuries SET current_balance = current_balance {$operator} :amt WHERE id = :tid");
            $this->db->bind(':amt', $payment->amount);
            $this->db->bind(':tid', $payment->treasury_id);
            $this->db->execute();

            // 2. عكس مديونية المورد أو العميل (نرجع المديونية بالجمع لأننا خصمناها سابقاً)
            if ($payment->payment_type == 'Out' && !empty($payment->supplier_id)) {
                try {
                    $this->db->query("UPDATE suppliers SET balance = balance + :amt WHERE id = :sid");
                    $this->db->bind(':amt', $payment->amount);
                    $this->db->bind(':sid', $payment->supplier_id);
                    $this->db->execute();
                } catch(Exception $e) {}
            } elseif ($payment->payment_type == 'In' && !empty($payment->customer_id)) {
                try {
                    $this->db->query("UPDATE customers SET balance = balance + :amt WHERE id = :cid");
                    $this->db->bind(':amt', $payment->amount);
                    $this->db->bind(':cid', $payment->customer_id);
                    $this->db->execute();
                } catch(Exception $e) {}
            }

            // 3. مسح السند من قاعدة البيانات
            $this->db->query("DELETE FROM {$this->table} WHERE id = :id AND company_id = :cid");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            return false;
        }
    }
}