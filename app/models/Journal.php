<?php
// app/models/Journal.php

class Journal extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'journal_entries';
    }

    public function getAllEntries(): array {
        $sql = "SELECT j.*, u.name as creator_name 
                FROM {$this->table} j 
                LEFT JOIN users u ON j.created_by = u.id 
                ORDER BY j.entry_date DESC, j.id DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getEntryById(int $id): ?object {
        $sql = "SELECT j.*, u.name as creator_name 
                FROM {$this->table} j 
                LEFT JOIN users u ON j.created_by = u.id 
                WHERE j.id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getEntryLines(int $entryId): array {
        $sql = "SELECT jl.*, a.code as account_code, a.name as account_name 
                FROM journal_lines jl 
                JOIN chart_of_accounts a ON jl.account_id = a.id 
                WHERE jl.journal_entry_id = :entry_id";
        $this->db->query($sql);
        $this->db->bind(':entry_id', $entryId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function createEntry(array $data, array $lines): bool {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table} (entry_number, entry_date, description, reference_type, reference_id, created_by, created_at) 
                    VALUES (:entry_number, :entry_date, :description, :reference_type, :reference_id, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':entry_number', $data['entry_number']);
            $this->db->bind(':entry_date', $data['entry_date']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':reference_type', $data['reference_type'] ?? null);
            $this->db->bind(':reference_id', $data['reference_id'] ?? null);
            $this->db->bind(':created_by', Session::getUserId(), PDO::PARAM_INT);
            
            if (!$this->db->execute()) throw new Exception("فشل إدراج القيد.");
            
            $entryId = $this->db->lastInsertId();
            $lineSql = "INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit, description) 
                        VALUES (:entry_id, :account_id, :debit, :credit, :description)";
            
            foreach ($lines as $line) {
                $this->updateAccountBalance($line['account_id'], $line['debit'], $line['credit']);

                $this->db->query($lineSql);
                $this->db->bind(':entry_id', $entryId, PDO::PARAM_INT);
                $this->db->bind(':account_id', $line['account_id'], PDO::PARAM_INT);
                $this->db->bind(':debit', $line['debit']);
                $this->db->bind(':credit', $line['credit']);
                $this->db->bind(':description', $line['description'] ?? '');
                
                if (!$this->db->execute()) throw new Exception("فشل إدراج سطر القيد.");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateEntry(int $id, array $data, array $lines): bool {
        try {
            $this->db->beginTransaction();

            // 1. جلب الأسطر القديمة وعكس تأثيرها على أرصدة الحسابات أولاً
            $oldLines = $this->getEntryLines($id);
            foreach ($oldLines as $old) {
                // نمرر المدين والدائن بالسالب لعكس تأثيرها
                $this->updateAccountBalance($old->account_id, -$old->debit, -$old->credit);
            }

            // 2. تحديث الرأس الأساسي للقيد
            $sql = "UPDATE {$this->table} 
                    SET entry_date = :entry_date, description = :description, 
                        reference_type = :reference_type, reference_id = :reference_id
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':entry_date', $data['entry_date']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':reference_type', $data['reference_type'] ?? null);
            $this->db->bind(':reference_id', $data['reference_id'] ?? null, PDO::PARAM_INT);
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            if (!$this->db->execute()) throw new Exception("فشل تحديث رأس القيد.");

            // 3. حذف الأسطر القديمة
            $this->db->query("DELETE FROM journal_lines WHERE journal_entry_id = :id");
            $this->db->bind(':id', $id, PDO::PARAM_INT);
            $this->db->execute();

            // 4. إدراج الأسطر الجديدة وتطبيق أرصدتها
            $lineSql = "INSERT INTO journal_lines (journal_entry_id, account_id, debit, credit, description) 
                        VALUES (:entry_id, :account_id, :debit, :credit, :description)";
            
            foreach ($lines as $line) {
                // تحديث رصيد الحساب بالقيم الجديدة
                $this->updateAccountBalance($line['account_id'], $line['debit'], $line['credit']);

                $this->db->query($lineSql);
                $this->db->bind(':entry_id', $id, PDO::PARAM_INT);
                $this->db->bind(':account_id', $line['account_id'], PDO::PARAM_INT);
                $this->db->bind(':debit', $line['debit']);
                $this->db->bind(':credit', $line['credit']);
                $this->db->bind(':description', $line['description'] ?? '');
                
                if (!$this->db->execute()) throw new Exception("فشل إدراج السطر الجديد.");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    private function updateAccountBalance(int $accountId, float $debit, float $credit): void {
        $this->db->query("SELECT type FROM chart_of_accounts WHERE id = :id");
        $this->db->bind(':id', $accountId, PDO::PARAM_INT);
        $account = $this->db->single();
        
        if (!$account) return;

        $amount = 0;
        // أصول ومصروفات تزيد بالمدين وتنقص بالدائن
        if (in_array($account->type, ['asset', 'expense'])) {
            $amount = $debit - $credit;
        } else {
            // خصوم، حقوق ملكية، وإيرادات تزيد بالدائن وتنقص بالمدين
            $amount = $credit - $debit;
        }

        $this->db->query("UPDATE chart_of_accounts SET balance = balance + :amount WHERE id = :id");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':id', $accountId, PDO::PARAM_INT);
        $this->db->execute();
    }
}