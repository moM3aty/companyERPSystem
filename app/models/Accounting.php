<?php
// app/models/Accounting.php

class Accounting extends Model {
    
    /**
     * جلب كل المصروفات مرتبة بالأحدث
     */
    public function getExpenses() {
        $this->db->query('SELECT * FROM expenses ORDER BY id DESC');
        return $this->db->resultSet();
    }

    /**
     * البحث في المصروفات بالبيان أو التصنيف
     */
    public function searchExpenses($query) {
        $this->db->query("SELECT * FROM expenses 
                          WHERE description LIKE :q 
                             OR category LIKE :q 
                          ORDER BY id DESC");
        $this->db->bind(':q', '%' . $query . '%');
        return $this->db->resultSet();
    }

    /**
     * جلب المصروفات ضمن نطاق تاريخي
     */
    public function getExpensesByDateRange($from, $to) {
        $this->db->query("SELECT * FROM expenses 
                          WHERE DATE(created_at) BETWEEN :from AND :to 
                          ORDER BY id DESC");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        return $this->db->resultSet();
    }

    /**
     * إضافة مصروف جديد مع التصنيف
     */
    public function addExpense($data) {
        $this->db->query('INSERT INTO expenses (description, amount, category) 
                          VALUES (:desc, :amount, :cat)');
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':cat', $data['category'] ?? 'أخرى');
        return $this->db->execute();
    }

    /**
     * حذف مصروف بالمعرّف
     */
    public function deleteExpense($id) {
        $this->db->query('DELETE FROM expenses WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->execute();
    }

    /**
     * جلب مصروف واحد بالمعرّف
     */
    public function getExpenseById($id) {
        $this->db->query('SELECT * FROM expenses WHERE id = :id');
        $this->db->bind(':id', $id, PDO::PARAM_INT);
        return $this->db->single();
    }

    /**
     * إجمالي المصروفات الكلي
     */
    public function getTotalExpenses() {
        $this->db->query('SELECT COALESCE(SUM(amount), 0) as total FROM expenses');
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    /**
     * إجمالي المصروفات ضمن نطاق تاريخي
     */
    public function getTotalExpensesByDateRange($from, $to) {
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total 
                          FROM expenses 
                          WHERE DATE(created_at) BETWEEN :from AND :to");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    /**
     * إجمالي المبيعات الكلي
     */
    public function getTotalSales() {
        $this->db->query('SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices');
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    /**
     * إجمالي المبيعات ضمن نطاق تاريخي
     */
    public function getTotalSalesByDateRange($from, $to) {
        $this->db->query("SELECT COALESCE(SUM(total_amount), 0) as total 
                          FROM invoices 
                          WHERE DATE(created_at) BETWEEN :from AND :to");
        $this->db->bind(':from', $from);
        $this->db->bind(':to', $to);
        $row = $this->db->single();
        return (float) ($row->total ?? 0);
    }

    /**
     * توزيع المصروفات حسب التصنيف (للتقارير)
     */
    public function getExpenseDistribution() {
        $this->db->query('SELECT category, COUNT(*) as count, SUM(amount) as total 
                          FROM expenses 
                          WHERE category IS NOT NULL AND category != ""
                          GROUP BY category 
                          ORDER BY total DESC');
        return $this->db->resultSet();
    }

    /**
     * المصروفات الشهرية للسنة الحالية (للتقارير)
     */
    public function getMonthlyExpenses() {
        $this->db->query("SELECT MONTH(created_at) - 1 as month_idx, 
                                 COALESCE(SUM(amount), 0) as total 
                          FROM expenses 
                          WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
                          GROUP BY MONTH(created_at)");
        return $this->db->resultSet();
    }

    /**
     * المبيعات الشهرية للسنة الحالية (للتقارير)
     */
    public function getMonthlySales() {
        $this->db->query("SELECT MONTH(created_at) - 1 as month_idx, 
                                 COALESCE(SUM(total_amount), 0) as total 
                          FROM invoices 
                          WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
                          GROUP BY MONTH(created_at)");
        return $this->db->resultSet();
    }

    /**
     * عدد الفواتير الكلي
     */
    public function getInvoiceCount() {
        $this->db->query('SELECT COUNT(*) as total FROM invoices');
        $row = $this->db->single();
        return (int) ($row->total ?? 0);
    }

    /**
     * أعلى المنتجات مبيعاً (للتقارير)
     */
    public function getTopProducts($limit = 10) {
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

    /**
     * جلب إعداد واحد بالمفتاح
     */
    public function getSetting($key) {
        $this->db->query('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $this->db->bind(':key', $key);
        $row = $this->db->single();
        return $row ? $row->setting_value : null;
    }

    /**
     * تحديث إعداد
     */
    public function updateSetting($key, $value) {
        $this->db->query('INSERT INTO settings (setting_key, setting_value) 
                          VALUES (:key, :val) 
                          ON DUPLICATE KEY UPDATE setting_value = :val2, updated_at = NOW()');
        $this->db->bind(':key', $key);
        $this->db->bind(':val', $value);
        $this->db->bind(':val2', $value);
        return $this->db->execute();
    }

    /**
     * جلب كل الإعدادات
     */
    public function getAllSettings() {
        $this->db->query('SELECT * FROM settings ORDER BY setting_key ASC');
        return $this->db->resultSet();
    }
    public function createJournalEntry($entryDate, $description, $referenceType, $referenceId, $userId, array $lines) {
    // $lines = [['account_id'=>1010, 'debit'=>1000, 'credit'=>0, 'description'=>'...'], ...]
    $this->db->beginTransaction();
    try {
        // إنشاء رقم القيد
        $entryNumber = 'JE-' . date('YmdHis') . rand(100,999);
        
        // إدخال القيد الرئيسي
        $this->db->query('
            INSERT INTO journal_entries 
            (entry_number, entry_date, description, reference_type, reference_id, created_by)
            VALUES (:num, :date, :desc, :ref_type, :ref_id, :user)
        ');
        $this->db->bind(':num', $entryNumber);
        $this->db->bind(':date', $entryDate);
        $this->db->bind(':desc', $description);
        $this->db->bind(':ref_type', $referenceType);
        $this->db->bind(':ref_id', $referenceId, PDO::PARAM_INT);
        $this->db->bind(':user', $userId, PDO::PARAM_INT);
        $this->db->execute();
        $entryId = $this->db->lastInsertId();
        
        // إدخال سطور القيد
        foreach ($lines as $line) {
            $this->db->query('
                INSERT INTO journal_lines 
                (journal_entry_id, account_id, debit, credit, description)
                VALUES (:entry, :account, :debit, :credit, :desc)
            ');
            $this->db->bind(':entry', $entryId, PDO::PARAM_INT);
            $this->db->bind(':account', $line['account_id'], PDO::PARAM_INT);
            $this->db->bind(':debit', $line['debit']);
            $this->db->bind(':credit', $line['credit']);
            $this->db->bind(':desc', $line['description'] ?? null);
            $this->db->execute();
        }
        
        $this->db->commit();
        return $entryId;
    } catch (Exception $e) {
        $this->db->rollBack();
        throw $e;
    }
}

/**
 * حساب رصيد حساب معين
 */
public function getAccountBalance($accountId) {
    $this->db->query('
        SELECT COALESCE(SUM(debit),0) - COALESCE(SUM(credit),0) as balance
        FROM journal_lines
        WHERE account_id = :aid
    ');
    $this->db->bind(':aid', $accountId, PDO::PARAM_INT);
    $row = $this->db->single();
    return $row ? (float) $row->balance : 0.0;
}
}