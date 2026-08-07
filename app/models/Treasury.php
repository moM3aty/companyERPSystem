<?php
// app/models/Treasury.php

class Treasury extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'treasuries';
    }

    public function getAllTreasuries(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY type ASC, id ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getAllTransactions(): array {
        $sql = "SELECT ft.*, t.name as treasury_name, u.name as user_name 
                FROM financial_transactions ft 
                JOIN treasuries t ON ft.treasury_id = t.id 
                LEFT JOIN users u ON ft.created_by = u.id 
                ORDER BY ft.transaction_date DESC, ft.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function createTransaction(array $data, bool $createJournal = true): bool {
        try {
            if (!$this->db->isConnected()) return false;
            // نبدأ المعاملة إذا لم تكن مبدوءة بالفعل من كلاس آخر (لتجنب تعارض الـ nested transactions)
            $inTransaction = $this->db->inTransaction();
            if (!$inTransaction) $this->db->beginTransaction();

            // 1. تسجيل الحركة
            $sql = "INSERT INTO financial_transactions (treasury_id, transaction_type, amount, transaction_date, reference, description, created_by, created_at) 
                    VALUES (:treasury_id, :transaction_type, :amount, :transaction_date, :reference, :description, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':treasury_id', $data['treasury_id'], PDO::PARAM_INT);
            $this->db->bind(':transaction_type', $data['transaction_type']);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':transaction_date', $data['transaction_date']);
            $this->db->bind(':reference', $data['reference'] ?? '');
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
            
            if (!$this->db->execute()) throw new Exception("فشل في إدراج السند المالي.");
            $transactionId = $this->db->lastInsertId();

            // 2. تحديث الرصيد
            $updateSql = $data['transaction_type'] === 'receipt' 
                ? "UPDATE treasuries SET current_balance = current_balance + :amount WHERE id = :id"
                : "UPDATE treasuries SET current_balance = current_balance - :amount WHERE id = :id";

            $this->db->query($updateSql);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':id', $data['treasury_id'], PDO::PARAM_INT);
            if (!$this->db->execute()) throw new Exception("فشل في تحديث رصيد الخزينة.");

            // 3. إنشاء قيد محاسبي إذا طُلب ذلك (في حالة الحركة اليدوية المباشرة)
            if ($createJournal && !empty($data['account_id'])) {
                $accountingModel = new Accounting();
                $dbCoa = $this->db;
                $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND name LIKE '%صندوق%' LIMIT 1");
                $cashAcc = $dbCoa->single();

                if ($cashAcc) {
                    $lines = [];
                    if ($data['transaction_type'] === 'receipt') {
                        $lines[] = ['account_id' => $cashAcc->id, 'debit' => $data['amount'], 'credit' => 0, 'description' => $data['description']];
                        $lines[] = ['account_id' => $data['account_id'], 'debit' => 0, 'credit' => $data['amount'], 'description' => $data['description']];
                    } else {
                        $lines[] = ['account_id' => $data['account_id'], 'debit' => $data['amount'], 'credit' => 0, 'description' => $data['description']];
                        $lines[] = ['account_id' => $cashAcc->id, 'debit' => 0, 'credit' => $data['amount'], 'description' => $data['description']];
                    }
                    $accountingModel->createJournalEntry($data['transaction_date'], "حركة خزينة: {$data['description']}", 'treasury_transaction', $transactionId, Session::getUserId(), $lines);
                }
            }

            if (!$inTransaction) $this->db->commit();
            return true;

        } catch (Exception $e) {
            if (!$inTransaction) $this->db->rollBack();
            return false;
        }
    }
}