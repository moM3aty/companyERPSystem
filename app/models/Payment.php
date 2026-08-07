<?php
// app/models/Payment.php

class Payment extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'payments';
    }

    public function getAllPayments(): array {
        // دمج ذكي لجلب اسم العميل (إذا كانت فاتورة) أو اسم المورد (إذا كان أمر شراء)
        $sql = "SELECT p.*, 
                       CASE 
                           WHEN p.reference_type = 'invoice' THEN i.invoice_number 
                           WHEN p.reference_type = 'purchase_order' THEN po.po_number 
                       END as ref_number,
                       CASE 
                           WHEN p.reference_type = 'invoice' THEN c.name 
                           WHEN p.reference_type = 'purchase_order' THEN s.name 
                       END as party_name
                FROM {$this->table} p
                LEFT JOIN invoices i ON p.reference_id = i.id AND p.reference_type = 'invoice'
                LEFT JOIN customers c ON i.customer_id = c.id
                LEFT JOIN purchase_orders po ON p.reference_id = po.id AND p.reference_type = 'purchase_order'
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                ORDER BY p.created_at DESC";
                
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createPayment(array $data): bool {
        try {
            $this->db->beginTransaction();

            // 1. تسجيل الدفعة في جدول المدفوعات
            $sql = "INSERT INTO {$this->table} (reference_id, reference_type, amount, payment_method, notes, created_at) 
                    VALUES (:reference_id, :reference_type, :amount, :payment_method, :notes, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':reference_id', $data['reference_id'], PDO::PARAM_INT);
            $this->db->bind(':reference_type', $data['reference_type']);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':payment_method', $data['payment_method']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->execute();
            
            $paymentId = $this->db->lastInsertId();

            // تجهيز المتغيرات للربط المحاسبي
            $dbCoa = $this->db;
            $accountingModel = new Accounting();
            $treasuryModel = new Treasury();
            
            // جلب حساب الصندوق/البنك الافتراضي
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND (name LIKE '%صندوق%' OR name LIKE '%بنك%') LIMIT 1");
            $cashAcc = $dbCoa->single();
            
            // 2. تحديث الأرصدة وإنشاء القيود حسب نوع الحركة
            if ($data['reference_type'] === 'invoice') {
                // --- سند قبض من عميل ---
                $sqlBalance = "UPDATE customers c 
                               JOIN invoices i ON c.id = i.customer_id 
                               SET c.balance = c.balance - :amount 
                               WHERE i.id = :ref_id";
                
                // جلب حساب العملاء للقيود
                $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND name LIKE '%عملاء%' LIMIT 1");
                $customerAcc = $dbCoa->single();
                
                if ($cashAcc && $customerAcc) {
                    $lines = [
                        ['account_id' => $cashAcc->id, 'debit' => $data['amount'], 'credit' => 0, 'description' => "سند قبض فاتورة مبيعات - {$data['notes']}"],
                        ['account_id' => $customerAcc->id, 'debit' => 0, 'credit' => $data['amount'], 'description' => "سداد جزء من مديونية العميل"]
                    ];
                    $accountingModel->createJournalEntry(date('Y-m-d'), "سند قبض برقم {$paymentId}", 'payment', $paymentId, Session::getUserId(), $lines);
                }
                
                // إيداع في الخزنة
                $treasuryModel->createTransaction([
                    'treasury_id' => 1, 'transaction_type' => 'receipt', 'amount' => $data['amount'],
                    'transaction_date' => date('Y-m-d'), 'reference' => "سند قبض #{$paymentId}",
                    'description' => "تحصيل مبيعات", 'created_by' => Session::getUserId()
                ], false); // false = don't create duplicate journal
                
            } else {
                // --- سند صرف لمورد ---
                $sqlBalance = "UPDATE suppliers s 
                               JOIN purchase_orders po ON s.id = po.supplier_id 
                               SET s.balance = s.balance - :amount 
                               WHERE po.id = :ref_id";
                               
                // جلب حساب الموردين للقيود
                $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'liability' AND name LIKE '%موردين%' LIMIT 1");
                $supplierAcc = $dbCoa->single();
                
                if ($cashAcc && $supplierAcc) {
                    $lines = [
                        ['account_id' => $supplierAcc->id, 'debit' => $data['amount'], 'credit' => 0, 'description' => "سداد التزام لمورد أمر شراء"],
                        ['account_id' => $cashAcc->id, 'debit' => 0, 'credit' => $data['amount'], 'description' => "سند صرف - {$data['notes']}"]
                    ];
                    $accountingModel->createJournalEntry(date('Y-m-d'), "سند صرف برقم {$paymentId}", 'payment', $paymentId, Session::getUserId(), $lines);
                }
                
                // سحب من الخزنة
                $treasuryModel->createTransaction([
                    'treasury_id' => 1, 'transaction_type' => 'payment', 'amount' => $data['amount'],
                    'transaction_date' => date('Y-m-d'), 'reference' => "سند صرف #{$paymentId}",
                    'description' => "سداد مشتريات", 'created_by' => Session::getUserId()
                ], false);
            }

            // تنفيذ تحديث أرصدة العملاء أو الموردين
            $this->db->query($sqlBalance);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':ref_id', $data['reference_id'], PDO::PARAM_INT);
            $this->db->execute();

            // 3. تسجيل الحدث
            ActivityLog::logAction('CREATE', 'Payment', $paymentId, "تسجيل سند " . ($data['reference_type'] === 'invoice' ? 'قبض' : 'صرف') . " بمبلغ {$data['amount']}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}