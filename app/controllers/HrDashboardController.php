<?php
// app/controllers/HrDashboardController.php

class HrDashboardController extends Controller {
    
    public function __construct() {
        $this->requireAuth();$this->requireAnyRole(['admin', 'super_admin', 'manager', 'hr']);
    }

    public function index() {
        $db = Database::getInstance();$cid = Session::get('company_id') ?: 1;

        // 1. إحصائيات القوى العاملة (محمي ضد الانهيار)
        $workforce = (object)['total' => 0, 'active' => 0, 'exiting' => 0];
        try {
            $db->query("SELECT COUNT(*) as total, 
                        SUM(CASE WHEN employment_status = 'Active' OR status = 'active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN employment_status = 'Exit Process' OR status = 'exit_process' THEN 1 ELSE 0 END) as exiting
                        FROM employees WHERE company_id = :cid");
            $db->bind(':cid',$cid);
            $result =$db->single();
            if ($result) $workforce =$result;
        } catch (Exception $e) {}

        // 2. إحصائيات التوظيف
        $recruitment = (object)['total_candidates' => 0, 'interviews' => 0];
        try {
            $db->query("SELECT COUNT(*) as total_candidates, 
                        SUM(CASE WHEN status = 'Interview' OR status = 'interview' THEN 1 ELSE 0 END) as interviews
                        FROM candidates WHERE company_id = :cid");
            $db->bind(':cid',$cid);
            $result =$db->single();
            if ($result) $recruitment =$result;
        } catch (Exception $e) {}

        // 3. تنبيهات الوثائق والإقامات (تنتهي خلال 60 يوم)
        $expiringDocs = [];
        try {
            // استخدام hr_employee_documents والعمود full_name
            $db->query("SELECT ed.*, e.full_name as employee_name, DATEDIFF(ed.expiry_date, CURDATE()) as days_left 
                        FROM hr_employee_documents ed 
                        JOIN employees e ON ed.employee_id = e.id 
                        WHERE ed.company_id = :cid AND ed.expiry_date IS NOT NULL 
                        AND DATEDIFF(ed.expiry_date, CURDATE()) <= 60 
                        ORDER BY days_left ASC");
            $db->bind(':cid',$cid);
            $expiringDocs =$db->resultSet();
        } catch (Exception $e) {}

        // 4. تنبيهات العقود (تنتهي خلال 60 يوم)
        $expiringContracts = [];
        try {
            // استخدام عمود full_name
            $db->query("SELECT ec.*, e.full_name as employee_name, DATEDIFF(ec.end_date, CURDATE()) as days_left 
                        FROM employee_contracts ec 
                        JOIN employees e ON ec.employee_id = e.id 
                        WHERE ec.company_id = :cid AND ec.end_date IS NOT NULL 
                        AND DATEDIFF(ec.end_date, CURDATE()) <= 60 
                        ORDER BY days_left ASC");
            $db->bind(':cid',$cid);
            $expiringContracts =$db->resultSet();
        } catch (Exception $e) {}

        // 5. غيابات غير مبررة آخر 7 أيام
        $absences = [];
        try {
            $db->query("SELECT a.*, e.full_name as employee_name 
                        FROM attendance a 
                        JOIN employees e ON a.employee_id = e.id 
                        WHERE a.company_id = :cid AND a.status = 'absent' 
                        AND a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        ORDER BY a.date DESC");
            $db->bind(':cid',$cid);
            $absences =$db->resultSet();
        } catch (Exception $e) {}

        $data = [
            'title' => 'لوحة القيادة - الموارد البشرية',
            'workforce' => $workforce,
            'recruitment' => $recruitment,
            'expiringDocs' => $expiringDocs,
            'expiringContracts' => $expiringContracts,
            'absences' => $absences,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'لوحة الـ HR', 'url' => 'hrDashboard/index']
            ]
        ];
        
        ob_start();
        // التأكد أن اسم المجلد واسم الملف متطابقان مع الشاشة التي أنشأناها
        $this->view('hr_dashboard/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }
}