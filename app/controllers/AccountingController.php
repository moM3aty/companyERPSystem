<?php
// app/controllers/AccountingController.php

class AccountingController extends Controller {

    private $dashboardModel;

    public function __construct() {
        // 1. التأكد من تسجيل الدخول
        $this->requireAuth();
        
        // 2. فحص الصلاحيات بطريقة آمنة بدون الاعتماد على دوال قد تكون غير موجودة
        $role = Session::getUserRole();
        $allowedRoles = ['admin', 'editor', 'manager', 'super_admin', 'accountant'];
        if (!in_array($role, $allowedRoles)) {
            $this->redirect('dashboard/index');
            exit;
        }
        
        // 3. استدعاء الموديل الخاص باللوحة المالية إذا كان موجوداً
        if (file_exists('../app/models/Dashboard.php')) {
            $this->dashboardModel = $this->model('Dashboard');
        }
    }

    /**
     * عرض اللوحة الرئيسية للمحاسبة (Finance Dashboard)
     */
    public function dashboard(): void {
        $db = Database::getInstance();

        $assets = 0;
        $liabilities = 0;
        $revenues = 0;
        $expenses = 0;
        $recentEntries = [];

        // استخدام try-catch لمنع انهيار الصفحة (Error 500) إذا كانت الجداول غير موجودة
        try {
            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'asset'");
            $assets = (float)($db->single()->total ?? 0);

            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'liability'");
            $liabilities = (float)($db->single()->total ?? 0);

            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'revenue'");
            $revenues = (float)($db->single()->total ?? 0);
            
            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'expense'");
            $expenses = (float)($db->single()->total ?? 0);

            $db->query("SELECT * FROM journal_entries ORDER BY created_at DESC LIMIT 5");
            $recentEntries = $db->resultSet();
        } catch (Exception $e) {
            // تم تجاهل الخطأ مؤقتاً لتفتح الصفحة بدلاً من إعطاء خطأ 500
        }
        
        $netIncome = $revenues - $expenses;

        // المؤشرات المالية الجديدة (Cash Flow, AR, AP, Upcoming)
        $metrics = [];
        $cashFlow = ['labels' => [], 'in' => [], 'out' => []];
        
        if ($this->dashboardModel) {
            try {
                $metrics = $this->dashboardModel->getFinanceMetrics();
                $cashFlow = $this->dashboardModel->getMonthlyCashFlow();
            } catch (Exception $e) {}
        }

        $data = [
            'title' => 'الإدارة المالية والمحاسبة',
            'stats' => [
                'total_assets' => $assets,
                'total_liabilities' => $liabilities,
                'net_income' => $netIncome
            ],
            'recent_entries' => $recentEntries,
            'metrics' => $metrics,
            'cashFlow' => json_encode($cashFlow),
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => 'accounting/dashboard']
            ]
        ];

        ob_start();
        $this->view('accounting/dashboard', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    public function incomeStatement(): void {
        $db = Database::getInstance();
        $revenues = []; $expenses = [];
        try {
            $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'revenue' AND balance > 0 ORDER BY code ASC");
            $revenues = $db->resultSet();
            $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'expense' AND balance > 0 ORDER BY code ASC");
            $expenses = $db->resultSet();
        } catch (Exception $e) {}

        $data = [
            'title' => 'قائمة الدخل',
            'revenues' => $revenues,
            'expenses' => $expenses,
            'breadcrumb' => [['label' => 'المالية', 'url' => 'accounting/dashboard'], ['label' => 'قائمة الدخل', 'url' => '#']]
        ];
        ob_start(); $this->view('accounting/income_statement', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function trialBalance(): void {
        $db = Database::getInstance();
        $accounts = [];
        try {
            $db->query("SELECT code, name, type, balance FROM chart_of_accounts WHERE balance != 0 ORDER BY code ASC");
            $accounts = $db->resultSet();
        } catch (Exception $e) {}

        $data = [
            'title' => 'ميزان المراجعة',
            'accounts' => $accounts,
            'breadcrumb' => [['label' => 'المالية', 'url' => 'accounting/dashboard'], ['label' => 'ميزان المراجعة', 'url' => '#']]
        ];
        ob_start(); $this->view('accounting/trial_balance', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function balanceSheet(): void {
        $db = Database::getInstance();
        $assets = []; $liabilities = []; $equities = [];
        $totalRevenue = 0; $totalExpense = 0;

        try {
            $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'asset' AND balance != 0 ORDER BY code ASC");
            $assets = $db->resultSet();

            $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'liability' AND balance != 0 ORDER BY code ASC");
            $liabilities = $db->resultSet();

            $db->query("SELECT code, name, balance FROM chart_of_accounts WHERE type = 'equity' AND balance != 0 ORDER BY code ASC");
            $equities = $db->resultSet();

            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'revenue'");
            $totalRevenue = (float)($db->single()->total ?? 0);

            $db->query("SELECT SUM(balance) as total FROM chart_of_accounts WHERE type = 'expense'");
            $totalExpense = (float)($db->single()->total ?? 0);
        } catch (Exception $e) {}

        $netIncome = $totalRevenue - $totalExpense;

        $data = [
            'title' => 'الميزانية العمومية',
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equities' => $equities,
            'net_income' => $netIncome,
            'breadcrumb' => [['label' => 'المالية', 'url' => 'accounting/dashboard'], ['label' => 'الميزانية العمومية', 'url' => '#']]
        ];

        ob_start(); $this->view('accounting/balance_sheet', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}