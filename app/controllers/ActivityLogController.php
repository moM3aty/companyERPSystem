<?php
// المسار: app/controllers/ActivityLogController.php

class ActivityLogController extends Controller {
    
    private ActivityLog $logModel;

    public function __construct() {
        // حماية صارمة: فقط المدير العام يمكنه رؤية سجل الحركات
        $this->requireRole('admin');
        $this->logModel = $this->model('ActivityLog');
    }

    public function index(): void {
        // جلب آخر 500 حركة لتجنب بطء الصفحة
        $logs = $this->logModel->getAllLogs(500);
        
        $data = [
            'title' => 'سجل نشاط النظام (Audit Trail)',
            'logs' => $logs,
            'breadcrumb' => [
                ['label' => 'الإعدادات والتقارير', 'url' => '#'],
                ['label' => 'سجل النشاط', 'url' => 'activityLog/index']
            ]
        ];
        
        ob_start();
        $this->view('activity_logs/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }
}