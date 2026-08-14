<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller {
    
    private $dashboardModel;

    public function __construct() {
        $this->requireAuth();
        // تحميل موديل الداشبورد لاستخدامه في لوحة الـ CEO
        if (file_exists('../app/models/Dashboard.php')) {
            $this->dashboardModel = $this->model('Dashboard');
        }
    }

    // 🟢 1. لوحة القيادة العامة (كودك الأصلي كامل بدون أي نقص) 🟢
    public function index(): void {
        $db = Database::getInstance();
        
        $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices");
        $totalSales = (float)($db->single()->total ?? 0);

        $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses");
        $totalExpenses = (float)($db->single()->total ?? 0);

        $netProfit = $totalSales - $totalExpenses;

        $db->query("SELECT COALESCE(SUM(balance), 0) as total FROM customers WHERE balance > 0");
        $totalReceivables = (float)($db->single()->total ?? 0);

        $db->query("SELECT COALESCE(SUM(balance), 0) as total FROM suppliers WHERE balance > 0");
        $totalPayables = (float)($db->single()->total ?? 0);

        $stats = [];
        $db->query("SELECT COUNT(*) as count FROM employees");
        $stats['employees'] = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM products");
        $stats['products'] = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM projects WHERE status IN ('active', 'planning')");
        $stats['projects'] = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM support_tickets WHERE status IN ('open', 'in_progress')");
        $stats['open_tickets'] = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM leave_requests WHERE status = 'pending'");
        $pendingLeaves = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM employee_advances WHERE status = 'pending'");
        $pendingAdvances = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM purchase_requests WHERE status = 'pending'");
        $pendingPurchaseRequests = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM products WHERE quantity <= reorder_point");
        $lowStockCount = (int)($db->single()->count ?? 0);

        $db->query("SELECT COUNT(*) as count FROM contracts WHERE status = 'active' AND end_date BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)");
        $expiringContractsCount = (int)($db->single()->count ?? 0);

        // إصلاح مصفوفة المبيعات للرسم البياني لتتوافق مع JSON
        $labels = [];
        $dataArray = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = date('Y-m', strtotime("-$i months"));
            $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices WHERE DATE_FORMAT(created_at, '%Y-%m') = :mdate");
            $db->bind(':mdate', $monthDate);
            $total = (float)($db->single()->total ?? 0);
            
            $labels[] = date('M Y', strtotime("-$i months"));
            $dataArray[] = $total;
        }

        $db->query("SELECT 'invoice' as type, invoice_number as title, total_amount as details, created_at FROM invoices ORDER BY created_at DESC LIMIT 5");
        $recentInvoices = $db->resultSet();

        $data = [
            'title' => 'لوحة القيادة والمؤشرات العامة',
            'kpis' => [
                'sales' => $totalSales,
                'expenses' => $totalExpenses,
                'profit' => $netProfit,
                'receivables' => $totalReceivables,
                'payables' => $totalPayables
            ],
            'stats' => $stats,
            'approvals' => [
                'leaves' => $pendingLeaves,
                'advances' => $pendingAdvances,
                'prs' => $pendingPurchaseRequests
            ],
            'alerts' => [
                'low_stock' => $lowStockCount,
                'expiring_contracts' => $expiringContractsCount
            ],
            'monthly_sales_labels' => json_encode($labels),
            'monthly_sales_data' => json_encode($dataArray),
            'recent_activities' => $recentInvoices
        ];

        ob_start();
        $this->view('dashboard/index', $data);
        $content = ob_get_clean();

        Layout::render($content, $data);
    }

    // 🟢 2. لوحة الإدارة العليا (CEO) التي برمجناها مؤخراً 🟢
    public function ceo() {
        $this->requireAnyRole(['admin', 'super_admin', 'ceo']);
        
        $metrics = $this->dashboardModel->getFinanceMetrics();
        $cashFlow = $this->dashboardModel->getMonthlyCashFlow();

        $data = [
            'title' => 'لوحة الإدارة العليا (CEO Dashboard)',
            'metrics' => $metrics,
            'cashFlow' => json_encode($cashFlow),
            'breadcrumb' => [['label' => 'الرئيسية', 'url' => '#'], ['label' => 'لوحة الـ CEO', 'url' => 'dashboard/ceo']]
        ];

        ob_start(); $this->view('dashboard/ceo', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    // 🟢 3. نقطة اتصال الإشعارات (من كودك الأصلي) 🟢
    public function readNotification(string $id = '') {
        if (!empty($id) && is_numeric($id)) {
            $notifModel = $this->model('Notification');
            $notifModel->markAsRead((int)$id);
            $notif = $notifModel->findById((int)$id);
            if ($notif && $notif->link) {
                $this->redirect($notif->link);
            }
        }
        $this->redirect('dashboard/index');
    }
}