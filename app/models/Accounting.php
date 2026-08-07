<?php
// app/models/Accounting.php

class Accounting extends Model {
    
    protected string $table = 'expenses';
    protected string $primaryKey = 'id';

    // ==========================================
    // المصروفات
    // ==========================================
    
    public function getExpenses(): array {
        $this->db->query('SELECT * FROM expenses ORDER BY id DESC');
        return $this->db->resultSet();
    }

    public function searchExpenses(string $query): array {
        $this->db->query("SELECT * FROM expenses 
                          WHERE description LIKE :q 
                             OR category LIKE :q 
                          ORDER BY id DESC");
        $this->db->bind(':q', '%' . $query . '%');
        return $this->db->resultSet();
    }

    public function getExpensesByDateRange(string $from, string $to): array {
        $this->db->query("SELECT * FROM expenses 
                          WHERE DATE(created_at) BETWEEN :from AND :to 
                          ORDER BY id DESC");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        return $this->db->resultSet();
    }

    public function addExpense(array $data): bool {
        $this->db->query('INSERT INTO expenses (description, amount, category) 
                          VALUES (:desc, :amount, :cat)');
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':cat', $data['category'] ?? 'أخرى');
        return $this->db->execute();
    }

    public function deleteExpense(int $id): bool {
        $this->db->query('DELETE FROM expenses WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getExpenseById(int $id): ?object {
        $this->db->query('SELECT * FROM expenses WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getTotalExpenses(): float {
        $this->db->query('SELECT COALESCE(SUM(amount), 0) as total FROM expenses');
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    public function getTotalExpensesByDateRange(string $from, string $to): float {
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total 
                          FROM expenses 
                          WHERE DATE(created_at) BETWEEN :from AND :to");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    public function getExpenseDistribution(): array {
        $this->db->query('SELECT category, COUNT(*) as count, SUM(amount) as total 
                          FROM expenses 
                          WHERE category IS NOT NULL AND category != ""
                          GROUP BY category 
                          ORDER BY total DESC');
        return $this->db->resultSet();
    }

    public function getMonthlyExpenses(): array {
        $this->db->query("SELECT MONTH(created_at) - 1 as month_idx, 
                                 COALESCE(SUM(amount), 0) as total 
                          FROM expenses 
                          WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
                          GROUP BY MONTH(created_at)");
        return $this->db->resultSet();
    }

    // ==========================================
    // المبيعات والفواتير
    // ==========================================

    public function getTotalSales(): float {
        $this->db->query('SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices');
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    public function getTotalSalesByDateRange(string $from, string $to): float {
        $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total 
                          FROM invoices 
                          WHERE DATE(created_at) BETWEEN :from AND :to");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    public function getMonthlySales(): array {
        $this->db->query("SELECT MONTH(created_at) - 1 as month_idx, 
                                 COALESCE(SUM(total_amount), 0) as total 
                          FROM invoices 
                          WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
                          GROUP BY MONTH(created_at)");
        return $this->db->resultSet();
    }

    public function getInvoiceCount(): int {
        $this->db->query('SELECT COUNT(*) as total FROM invoices');
        $row = $this->db->single();
        return (int) ($row->total ?? 0);
    }

    public function getTopProducts(int $limit = 10): array {
        $this->db->query('SELECT p.name, 
                                 SUM(ii.quantity) as total_units, 
                                 SUM(ii.subtotal) as total_revenue 
                          FROM invoice_items ii 
                          INNER JOIN products p ON ii.product_id = p.id 
                          GROUP BY ii.product_id, p.name 
                          ORDER BY total_revenue DESC 
                          LIMIT :lim');
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    // ==========================================
    // القيود اليومية والدورة المحاسبية الآلية
    // ==========================================

    public function getJournalEntries(int $limit = 100): array {
        $this->db->query('
            SELECT je.*, u.name as created_by_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            WHERE je.company_id = :cid
            ORDER BY je.id DESC
            LIMIT :lim
        ');
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getJournalEntryById(int $id): ?object {
        $this->db->query('
            SELECT je.*, u.name as created_by_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            WHERE je.id = :id AND je.company_id = :cid
        ');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        $this->db->bind(':cid', Session::get('company_id'), PDO::PARAM_INT);
        return $this->db->single();
    }

    public function getJournalLines(int $entryId): array {
        $this->db->query('
            SELECT jl.*, coa.name as account_name, coa.code as account_code
            FROM journal_lines jl
            LEFT JOIN chart_of_accounts coa ON jl.account_id = coa.id
            WHERE jl.journal_entry_id = :entry_id
            ORDER BY jl.id ASC
        ');
        $this->db->bind(':entry_id', $entryId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * دالة عامة لإنشاء قيد يومية آلي أو يدوي
     */
    public function createJournalEntry(
        string $entryDate,
        string $description,
        string $referenceType,
        ?int $referenceId,
        int $userId,
        array $lines
    ): int {
        
        $totalDebit = 0;
        $totalCredit = 0;
        $companyId = Session::get('company_id');

        foreach ($lines as $line) {
            $totalDebit += (float)($line['debit'] ?? 0);
            $totalCredit += (float)($line['credit'] ?? 0);
        }

        if (round($totalDebit, 2) !== round($totalCredit, 2)) {
            throw new \Exception('القيد غير متزن: مجموع المدين لا يساوي مجموع الدائن.');
        }

        $this->db->beginTransaction();
        try {
            $entryNumber = 'JE-' . date('YmdHis') . rand(100, 999);
            
            $this->db->query('
                INSERT INTO journal_entries 
                (company_id, entry_number, entry_date, description, reference_type, reference_id, created_by)
                VALUES (:company_id, :num, :date, :desc, :ref_type, :ref_id, :user)
            ');
            $this->db->bind(':company_id', $companyId, PDO::PARAM_INT);
            $this->db->bind(':num', $entryNumber);
            $this->db->bind(':date', $entryDate);
            $this->db->bind(':desc', $description);
            $this->db->bind(':ref_type', $referenceType);
            $this->db->bind(':ref_id', $referenceId, PDO::PARAM_INT);
            $this->db->bind(':user', $userId, PDO::PARAM_INT);
            $this->db->execute();
            
            $entryId = (int) $this->db->lastInsertId();
            
            foreach ($lines as $line) {
                $this->db->query('
                    INSERT INTO journal_lines 
                    (journal_entry_id, account_id, debit, credit, description)
                    VALUES (:entry, :account, :debit, :credit, :desc)
                ');
                $this->db->bind(':entry', $entryId, PDO::PARAM_INT);
                $this->db->bind(':account', $line['account_id'], PDO::PARAM_INT);
                $this->db->bind(':debit', $line['debit'] ?? 0);
                $this->db->bind(':credit', $line['credit'] ?? 0);
                $this->db->bind(':desc', $line['description'] ?? null);
                $this->db->execute();

                // تحديث أرصدة شجرة الحسابات
                $this->updateAccountBalance($line['account_id'], (float)($line['debit'] ?? 0), (float)($line['credit'] ?? 0), $companyId);
            }
            
            $this->db->commit();
            return $entryId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \Exception('فشل إنشاء القيد المحاسبي: ' . $e->getMessage());
        }
    }

    private function updateAccountBalance(int $accountId, float $debit, float $credit, int $companyId): void {
        $this->db->query("SELECT type FROM chart_of_accounts WHERE id = :id AND company_id = :cid");
        $this->db->bind(':id', $accountId, PDO::PARAM_INT);
        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        $account = $this->db->single();
        
        if (!$account) return;

        $amount = in_array($account->type, ['asset', 'expense']) ? ($debit - $credit) : ($credit - $debit);

        $this->db->query("UPDATE chart_of_accounts SET balance = balance + :amount WHERE id = :id AND company_id = :cid");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':id', $accountId, PDO::PARAM_INT);
        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        $this->db->execute();
    }

    public function getAccountBalance(int $accountId): float {
        $this->db->query('
            SELECT COALESCE(SUM(debit), 0) - COALESCE(SUM(credit), 0) as balance
            FROM journal_lines
            WHERE account_id = :aid
        ');
        $this->db->bind(':aid', $accountId, PDO::PARAM_INT);
        $row = $this->db->single();
        return $row ? (float) $row->balance : 0.0;
    }

    public function getTrialBalance(): array {
        $this->db->query('
            SELECT 
                coa.id,
                coa.code,
                coa.name,
                coa.type,
                COALESCE(SUM(jl.debit), 0) as total_debit,
                COALESCE(SUM(jl.credit), 0) as total_credit,
                COALESCE(SUM(jl.debit), 0) - COALESCE(SUM(jl.credit), 0) as balance
            FROM chart_of_accounts coa
            LEFT JOIN journal_lines jl ON coa.id = jl.account_id
            LEFT JOIN journal_entries je ON jl.journal_entry_id = je.id
            WHERE coa.is_active = 1
            GROUP BY coa.id, coa.code, coa.name, coa.type
            ORDER BY coa.code
        ');
        return $this->db->resultSet();
    }

    // ==========================================
    // الإعدادات
    // ==========================================

    public function getSetting(string $key): ?string {
        $companyId = Session::get('company_id') ?: 1; // الاعتماد على 1 إذا كان المالك الشامل (null)
        $this->db->query('SELECT setting_value FROM settings WHERE setting_key = :key AND (company_id = :cid OR company_id IS NULL) LIMIT 1');
        $this->db->bind(':key', $key);
        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        $row = $this->db->single();
        return $row ? $row->setting_value : null;
    }

    public function updateSetting(string $key, string $value): bool {
        $companyId = Session::get('company_id') ?: 1; // الاعتماد على 1 إذا كان المالك الشامل (null)
        $this->db->query('
            INSERT INTO settings (company_id, setting_key, setting_value) 
            VALUES (:cid, :key, :val) 
            ON DUPLICATE KEY UPDATE setting_value = :val2, updated_at = NOW()
        ');
        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        $this->db->bind(':key', $key);
        $this->db->bind(':val', $value);
        $this->db->bind(':val2', $value);
        return $this->db->execute();
    }

    public function getAllSettings(): array {
        $companyId = Session::get('company_id') ?: 1; // الاعتماد على 1 إذا كان المالك الشامل (null)
        $this->db->query('SELECT * FROM settings WHERE company_id = :cid OR company_id IS NULL ORDER BY setting_key ASC');
        $this->db->bind(':cid', $companyId, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}