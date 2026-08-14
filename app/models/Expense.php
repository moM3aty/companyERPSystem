<?php
// app/models/Expense.php

class Expense extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'expenses';
    }

    public function getAllExpenses() {
        try {
            // أضفنا الربط مع جدول expense_categories لجلب اسم التصنيف
            $sql = "SELECT e.*, t.name as treasury_name, u.name as user_name, ec.name as category_name
                    FROM {$this->table} e 
                    LEFT JOIN treasuries t ON e.treasury_id = t.id 
                    LEFT JOIN expense_categories ec ON e.category_id = ec.id
                    LEFT JOIN users u ON e.created_by = u.id 
                    WHERE e.company_id = :cid ORDER BY e.created_at DESC";
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->resultSet();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getExpenseById($id) {
        try {
            $this->db->query("SELECT e.*, t.name as treasury_name, u.name as user_name, ec.name as category_name
                              FROM {$this->table} e 
                              LEFT JOIN treasuries t ON e.treasury_id = t.id 
                              LEFT JOIN expense_categories ec ON e.category_id = ec.id
                              LEFT JOIN users u ON e.created_by = u.id 
                              WHERE e.id = :id AND e.company_id = :cid LIMIT 1");
            $this->db->bind(':id', $id);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            return $this->db->single();
        } catch (Exception $e) {
            return null;
        }
    }

    public function createExpense($data) {
        $treasuryCol = 'current_balance';
        try {
            $this->db->query("SHOW COLUMNS FROM treasuries LIKE 'balance'");
            if (!empty($this->db->resultSet())) $treasuryCol = 'balance';
        } catch(Exception $e){}

        $this->db->beginTransaction();
        try {
            // تم تغيير category إلى category_id
            $sql = "INSERT INTO {$this->table} 
                    (company_id, treasury_id, expense_date, category_id, amount, tax_amount, total_amount, cost_center, reference, notes, attachment, created_by) 
                    VALUES (:cid, :tid, :edate, :cat_id, :amt, :tax, :total, :cc, :ref, :notes, :attach, :user)";
            
            $this->db->query($sql);
            $this->db->bind(':cid', Session::get('company_id') ?: 1);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->bind(':edate', $data['expense_date']);
            $this->db->bind(':cat_id', $data['category_id']); // الحقل الجديد المربوط
            $this->db->bind(':amt', $data['amount']);
            $this->db->bind(':tax', $data['tax_amount']);
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':cc', $data['cost_center']);
            $this->db->bind(':ref', $data['reference']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':attach', $data['attachment']);
            $this->db->bind(':user', Session::getUserId());
            $this->db->execute();
            
            $expenseId = $this->db->lastInsertId();

            // خصم قيمة المصروف من الخزنة
            $this->db->query("UPDATE treasuries SET {$treasuryCol} = {$treasuryCol} - :total WHERE id = :tid");
            $this->db->bind(':total', $data['total_amount']);
            $this->db->bind(':tid', $data['treasury_id']);
            $this->db->execute();

            $this->db->commit();

            try {
                $this->createExpenseJournal($data, $expenseId);
            } catch (Throwable $th) {}

            return $expenseId;

        } catch (Throwable $e) {
            try { $this->db->rollBack(); } catch (Throwable $t) {}
            throw new Exception($e->getMessage());
        }
    }

    private function createExpenseJournal($data, $expenseId) {
        if (!file_exists('../app/models/JournalEntry.php')) return;
        require_once '../app/models/JournalEntry.php';
        if (!class_exists('JournalEntry')) return;

        $jeModel = new JournalEntry();
        
        $this->db->query("SELECT chart_account_id FROM treasuries WHERE id = :tid");
        $this->db->bind(':tid', $data['treasury_id']);
        $treasuryAcc = $this->db->single();
        $cashAccId = $treasuryAcc ? $treasuryAcc->chart_account_id : null;

        $expAccId = null;
        $table = "chart_of_accounts";
        try {
            $this->db->query("SELECT 1 FROM `accounting_accounts` LIMIT 1");
            $table = "accounting_accounts";
        } catch(Exception $e) {}

        $this->db->query("SELECT id FROM {$table} WHERE type = 'Expense' OR account_type = 'Expense' LIMIT 1");
        $exp = $this->db->single();
        if ($exp) $expAccId = $exp->id;

        // جلب اسم التصنيف للقيد
        $catName = "مصروف عام";
        try {
            $this->db->query("SELECT name FROM expense_categories WHERE id = :id");
            $this->db->bind(':id', $data['category_id']);
            $cat = $this->db->single();
            if ($cat) $catName = $cat->name;
        } catch (Exception $e) {}

        if ($cashAccId && $expAccId) {
            $jeData = [
                'journal_number' => 'JV-EXP-' . time(),
                'date' => $data['expense_date'],
                'description' => "مصروف {$catName}: {$data['notes']}",
                'total_amount' => $data['total_amount']
            ];

            $lines = [
                ['account_id' => $expAccId, 'description' => "إثبات مصروف {$catName}", 'debit' => $data['amount'], 'credit' => 0],
                ['account_id' => $cashAccId, 'description' => "دفع مصروف من الخزنة", 'debit' => 0, 'credit' => $data['total_amount']]
            ];
            
            if ($data['tax_amount'] > 0) {
                $this->db->query("SELECT id FROM {$table} WHERE name LIKE '%ضريبة%' OR account_name LIKE '%VAT%' LIMIT 1");
                $taxAcc = $this->db->single();
                if ($taxAcc) {
                    $lines[] = ['account_id' => $taxAcc->id, 'description' => "ضريبة مصروف", 'debit' => $data['tax_amount'], 'credit' => 0];
                } else {
                    $lines[0]['debit'] += $data['tax_amount'];
                }
            }
            $jeModel->createEntry($jeData, $lines);
        }
    }

    public function deleteExpense($id) {
        $this->db->beginTransaction();
        try {
            $exp = $this->getExpenseById($id);
            if (!$exp) throw new Exception("المصروف غير موجود");

            $treasuryCol = 'current_balance';
            try {
                $this->db->query("SHOW COLUMNS FROM treasuries LIKE 'balance'");
                if (!empty($this->db->resultSet())) $treasuryCol = 'balance';
            } catch(Exception $e){}

            $this->db->query("UPDATE treasuries SET {$treasuryCol} = {$treasuryCol} + :amt WHERE id = :tid");
            $this->db->bind(':amt', $exp->total_amount);
            $this->db->bind(':tid', $exp->treasury_id);
            $this->db->execute();

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