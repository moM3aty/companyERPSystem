<?php
// المسار: app/models/Journal.php

class Journal extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'journal_entries';
    }

    /**
     * جلب جميع القيود اليومية
     */
    public function getAllEntries(): array {
        $sql = "SELECT j.*, u.name as creator_name 
                FROM {$this->table} j 
                LEFT JOIN users u ON j.created_by = u.id 
                ORDER BY j.entry_date DESC, j.id DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * جلب قيد يومية محدد
     */
    public function getEntryById(int $id): ?object {
        $sql = "SELECT j.*, u.name as creator_name 
                FROM {$this->table} j 
                LEFT JOIN users u ON j.created_by = u.id 
                WHERE j.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * جلب سطور قيد معين
     */
    public function getEntryLines(int $entryId): array {
        $sql = "SELECT jl.*, a.code as account_code, a.name as account_name 
                FROM journal_lines jl 
                JOIN chart_of_accounts a ON jl.account_id = a.id 
                WHERE jl.journal_entry_id = :entry_id";
        $this->db->query($sql);
        $this->db->bind(':entry_id', $entryId);
        return $this->db->resultSet();
    }

    /**
     * إنشاء قيد يومية جديد مع سطوره
     * يتم استخدام Transactions لضمان تسجيل القيد والسطور كعملية واحدة
     */
    public function createEntry(array $data, array $lines): bool {
        try {
            $this->db->beginTransaction();

            // إدراج القيد الرئيسي
            $sql = "INSERT INTO {$this->table} (entry_number, entry_date, description, reference_type, reference_id, created_by, created_at) 
                    VALUES (:entry_number, :entry_date, :description, :reference_type, :reference_id, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':entry_number', $data['entry_number']);
            $this->db->bind(':entry_date', $data['entry_date']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':reference_type', $data['reference_type'] ?? null);
            $this->db->bind(':reference_id', $data['reference_id'] ?? null);
            $this->db->bind(':created_by', Session::getUserId());
            
            if (!$this->db->execute()) {
                throw new Exception("فشل في إدراج القيد الرئيسي.");
            }
            
            $entryId = $this->db->lastInsertId();

            // إدراج سطور القيد (المدين والدائن)
            $lineSql = "INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit, description) 
                        VALUES (:entry_id, :account_id, :debit, :credit, :description)";
            
            foreach ($lines as $line) {
                // تحديث رصيد الحساب بناءً على نوعه
                $this->updateAccountBalance($line['account_id'], $line['debit'], $line['credit']);

                $this->db->query($lineSql);
                $this->db->bind(':entry_id', $entryId);
                $this->db->bind(':account_id', $line['account_id']);
                $this->db->bind(':debit', $line['debit']);
                $this->db->bind(':credit', $line['credit']);
                $this->db->bind(':description', $line['description'] ?? '');
                
                if (!$this->db->execute()) {
                    throw new Exception("فشل في إدراج أحد سطور القيد.");
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            // يمكن تسجيل الخطأ $e->getMessage() في ملف الـ Logs لاحقاً
            return false;
        }
    }

    /**
     * تحديث رصيد الحساب عند إضافة قيد جديد
     */
    private function updateAccountBalance(int $accountId, float $debit, float $credit): void {
        // جلب نوع الحساب لمعرفة ما إذا كان يزداد بالمدين أم الدائن
        $this->db->query("SELECT type FROM chart_of_accounts WHERE id = :id");
        $this->db->bind(':id', $accountId);
        $account = $this->db->single();
        
        if (!$account) return;

        $amount = 0;
        
        // الأصول والمصروفات تزداد بالمدين وتقل بالدائن
        if (in_array($account->type, ['asset', 'expense'])) {
            $amount = $debit - $credit;
        } 
        // الخصوم، حقوق الملكية، والإيرادات تزداد بالدائن وتقل بالمدين
        else {
            $amount = $credit - $debit;
        }

        $this->db->query("UPDATE chart_of_accounts SET balance = balance + :amount WHERE id = :id");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':id', $accountId);
        $this->db->execute();
    }
}