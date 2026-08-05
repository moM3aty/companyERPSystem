<?php
// المسار: app/controllers/AccountingController.php

class AccountingController extends Controller {

    public function __construct() {
        // حماية الوصول للقسم المالي
        $this->requireAnyRole(['admin', 'editor', 'manager']);
    }

    /**
     * عرض اللوحة الرئيسية للمحاسبة (Dashboard)
     */
    public function dashboard(): void {
        $db = Database::getInstance();

        // 1. حساب إجمالي الأصول (Assets)
        $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'asset'");
        $assets = (float)($db->single()->total ?? 0);

        // 2. حساب إجمالي الخصوم (Liabilities)
        $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'liability'");
        $liabilities = (float)($db->single()->total ?? 0);

        // 3. حساب صافي الدخل التقريبي (الإيرادات - المصروفات)
        $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'revenue'");
        $revenues = (float)($db->single()->total ?? 0);
        
        $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'expense'");
        $expenses = (float)($db->single()->total ?? 0);
        
        $netIncome = $revenues - $expenses;

        // 4. جلب أحدث القيود
        $db->query("SELECT * FROM journal_entries ORDER BY created_at DESC LIMIT 5");
        $recentEntries = $db->resultSet();

        $data = [
            'title' => 'الإدارة المالية والمحاسبة',
            'stats' => [
                'total_assets' => $assets,
                'total_liabilities' => $liabilities,
                'net_income' => $netIncome
            ],
            'recent_entries' => $recentEntries
        ];

        $this->view('accounting/dashboard', $data);
    }

    /**
     * عرض تقرير قائمة الدخل (Income Statement)
     */
    public function incomeStatement(): void {
        $db = Database::getInstance();

        // جلب حسابات الإيرادات التي لها رصيد
        $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'revenue' AND balance > 0 ORDER BY code ASC");
        $revenues = $db->resultSet();

        // جلب حسابات المصروفات التي لها رصيد
        $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'expense' AND balance > 0 ORDER BY code ASC");
        $expenses = $db->resultSet();

        $data = [
            'title' => 'قائمة الدخل',
            'revenues' => $revenues,
            'expenses' => $expenses
        ];

        $this->view('accounting/income_statement', $data);
    }
}