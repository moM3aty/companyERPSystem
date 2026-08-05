<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول تلقائياً لكل طلب
        $this->requireAuth();
    }

    public function index() {
        $db = Database::getInstance();

        // ========================================
        // 1. إحصائيات الموارد البشرية
        // ========================================
        // عدد الموظفين
        $db->query('SELECT COUNT(*) as total FROM employees');
        $totalEmployees = $db->single()->total ?? 0;

        // الحضور اليومي (اليوم)
        $today = date('Y-m-d');
        $db->query('
            SELECT COUNT(*) as total 
            FROM attendance 
            WHERE date = :today AND status = "present"
        ');
        $db->bind(':today', $today);
        $presentToday = $db->single()->total ?? 0;

        // طلبات الإجازات المعلقة
        $db->query('SELECT COUNT(*) as total FROM leave_requests WHERE status = "pending"');
        $pendingLeaves = $db->single()->total ?? 0;

        // ========================================
        // 2. إحصائيات المالية والمحاسبة
        // ========================================
        $accountingModel = $this->model('Accounting');
        $totalSales = $accountingModel->getTotalSales();
        $totalExpenses = $accountingModel->getTotalExpenses();
        $netProfit = $totalSales - $totalExpenses;

        // عدد الفواتير
        $db->query('SELECT COUNT(*) as total FROM invoices');
        $invoiceCount = $db->single()->total ?? 0;

        // ========================================
        // 3. إحصائيات المشتريات والمخزون
        // ========================================
        // أوامر الشراء المعلقة
        $db->query('SELECT COUNT(*) as total FROM purchase_orders WHERE status IN ("pending", "approved")');
        $pendingOrders = $db->single()->total ?? 0;

        // إجمالي قيمة المخزون (من products)
        $db->query('SELECT COALESCE(SUM(quantity * price), 0) as total FROM products');
        $totalStockValue = $db->single()->total ?? 0;

        // إجمالي قيمة المخزون حسب المستودعات (من warehouse_stock)
        $db->query('
            SELECT w.name, COALESCE(SUM(ws.quantity * p.price), 0) as value
            FROM warehouse_stock ws
            JOIN warehouses w ON ws.warehouse_id = w.id
            JOIN products p ON ws.product_id = p.id
            GROUP BY w.id, w.name
            ORDER BY value DESC
        ');
        $warehouseStock = $db->resultSet();

        // ========================================
        // 4. إحصائيات CRM (الفرص)
        // ========================================
        $db->query('
            SELECT 
                COUNT(*) as total,
                SUM(estimated_value) as total_value,
                COUNT(CASE WHEN stage IN ("qualification", "needs_analysis") THEN 1 END) as open,
                COUNT(CASE WHEN stage = "closed_won" THEN 1 END) as won,
                COUNT(CASE WHEN stage = "closed_lost" THEN 1 END) as lost
            FROM opportunities
        ');
        $opportunityStats = $db->single();

        // ========================================
        // 5. إحصائيات المشاريع
        // ========================================
        $db->query('
            SELECT 
                COUNT(*) as total,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed,
                COALESCE(SUM(budget), 0) as total_budget
            FROM projects
        ');
        $projectStats = $db->single();

        // ========================================
        // 6. إحصائيات الأصول الثابتة
        // ========================================
        $db->query('
            SELECT 
                COUNT(*) as total,
                COALESCE(SUM(purchase_price), 0) as total_cost,
                COALESCE(SUM(current_value), 0) as total_current_value
            FROM fixed_assets
        ');
        $assetStats = $db->single();

        // ========================================
        // 7. المبيعات الشهرية (آخر 12 شهراً)
        // ========================================
        $monthlySales = $accountingModel->getMonthlySales();
        $salesChart = array_fill(0, 12, 0);
        foreach ($monthlySales as $m) {
            $idx = (int) $m->month_idx;
            if ($idx >= 0 && $idx <= 11) {
                $salesChart[$idx] = (float) $m->total;
            }
        }

        // ========================================
        // 8. الأنشطة الأخيرة (آخر 5 أحداث من audit_logs)
        // ========================================
        $db->query('
            SELECT al.*, u.name as user_name
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.id DESC LIMIT 5
        ');
        $recentActivities = $db->resultSet();

        // ========================================
        // تجهيز البيانات للـ View
        // ========================================
        $data = [
            'title' => 'لوحة التحكم الرئيسية',
            'user_name' => $_SESSION['user_name'] ?? 'مستخدم',

            // HR
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'pending_leaves' => $pendingLeaves,

            // المالية
            'total_sales' => $totalSales,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'invoice_count' => $invoiceCount,

            // المشتريات والمخزون
            'pending_orders' => $pendingOrders,
            'total_stock_value' => $totalStockValue,
            'warehouse_stock' => $warehouseStock,

            // CRM
            'opportunity_stats' => $opportunityStats,

            // المشاريع
            'project_stats' => $projectStats,

            // الأصول
            'asset_stats' => $assetStats,

            // الشارت
            'sales_chart' => $salesChart,

            // الأنشطة
            'recent_activities' => $recentActivities,

            'flash' => $this->getFlash()
        ];

        $this->view('dashboard/index', $data);
    }
}