<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $db = Database::getInstance();
        
        // جلب إحصائيات سريعة للوحة التحكم (الاعتماد على الجداول المتوفرة)
        $stats = [];
        
        // 1. الموظفين
        $db->query("SELECT COUNT(*) as count FROM employees");
        $stats['employees'] = $db->single()->count ?? 0;
        
        // 2. المنتجات
        $db->query("SELECT COUNT(*) as count FROM products");
        $stats['products'] = $db->single()->count ?? 0;
        
        // 3. المبيعات
        $db->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM invoices");
        $stats['sales'] = $db->single()->total ?? 0;

        // 4. المشاريع
        $db->query("SELECT COUNT(*) as count FROM projects WHERE status IN ('active', 'planning')");
        $stats['projects'] = $db->single()->count ?? 0;

        // النشاطات الحديثة (آخر 5 فواتير)
        $db->query("SELECT invoice_number as title, total_amount as details, created_at FROM invoices ORDER BY created_at DESC LIMIT 5");
        $recent_activities = $db->resultSet();

        $data = [
            'title' => 'لوحة التحكم الرئيسية',
            'stats' => $stats,
            'recent_activities' => $recent_activities,
            'user' => [
                'name' => Session::getUserName(),
                'role' => Session::getUserRole()
            ]
        ];

        // يتم الاعتماد على ملف الـ view الخاص بـ dashboard
        $this->view('dashboard/index', $data);
    }
}