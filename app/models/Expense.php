<?php
// app/models/Expense.php

class Expense extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'expenses';
    }
    
    public function getAllExpenses(): array {
        $sql = "SELECT e.*, c.name as category_name, u.name as created_by_name 
                FROM {$this->table} e 
                LEFT JOIN expense_categories c ON e.category_id = c.id 
                LEFT JOIN users u ON e.created_by = u.id 
                ORDER BY e.expense_date DESC, e.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }
    
    public function getTotalExpenses(): float {
        $sql = "SELECT SUM(amount) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total : 0.0;
    }
    
    public function getCategories(): array {
        $this->db->query("SELECT * FROM expense_categories ORDER BY name ASC");
        return $this->db->resultSet();
    }
    
    public function createExpense(array $data): bool {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table} (category_id, amount, expense_date, reference_no, notes, created_by, created_at) 
                    VALUES (:category_id, :amount, :expense_date, :reference_no, :notes, :created_by, NOW())";
            
            $this->db->query($sql);
            $this->db->bind(':category_id', $data['category_id'], PDO::PARAM_INT);
            $this->db->bind(':amount', $data['amount']);
            $this->db->bind(':expense_date', $data['expense_date']);
            $this->db->bind(':reference_no', $data['reference_no']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':created_by', $data['created_by'], PDO::PARAM_INT);
            $this->db->execute();

            $expenseId = $this->db->lastInsertId();

            // نقوم بجلب حساب المصروفات (افتراضياً) وحساب الصندوق
            $dbCoa = $this->db;
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'expense' LIMIT 1");
            $expenseAcc = $dbCoa->single();
            
            $dbCoa->query("SELECT id FROM chart_of_accounts WHERE type = 'asset' AND name LIKE '%صندوق%' LIMIT 1");
            $cashAcc = $dbCoa->single();

            if ($expenseAcc && $cashAcc) {
                $lines = [
                    ['account_id' => $expenseAcc->id, 'debit' => $data['amount'], 'credit' => 0, 'description' => "مصروف تشغيلي: {$data['notes']}"],
                    ['account_id' => $cashAcc->id, 'debit' => 0, 'credit' => $data['amount'], 'description' => "صرف مبلغ لمصروف {$data['reference_no']}"]
                ];
                
                $accountingModel = new Accounting();
                $accountingModel->createJournalEntry(
                    $data['expense_date'],
                    "تسجيل مصروف تشغيلي بمبلغ {$data['amount']}",
                    'expense',
                    $expenseId,
                    $data['created_by'],
                    $lines
                );

                // تحديث رصيد الخزينة برمجياً إذا لزم الأمر
                $dbCoa->query("UPDATE treasuries SET current_balance = current_balance - :amt WHERE id = 1"); // نفترض الخزنة الرئيسية 1
                $dbCoa->bind(':amt', $data['amount']);
                $dbCoa->execute();
            }

            ActivityLog::logAction('CREATE', 'Expense', $expenseId, "تم تسجيل مصروف بقيمة {$data['amount']}");

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getExpenseById(int $id): ?object {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }
    
    public function updateExpense(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET category_id = :category_id, amount = :amount, expense_date = :expense_date, reference_no = :reference_no, notes = :notes WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':category_id', $data['category_id'], PDO::PARAM_INT);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':expense_date', $data['expense_date']);
        $this->db->bind(':reference_no', $data['reference_no']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
    
    public function deleteExpense(int $id): bool {
        $this->db->query("DELETE FROM {$this->table} WHERE id = :id");
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }
}