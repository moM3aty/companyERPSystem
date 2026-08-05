<?php
// app/controllers/AuditController.php

class AuditController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
        // فقط المشرفين يمكنهم رؤية سجل التدقيق
        if ($_SESSION['user_role'] !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى سجل التدقيق');
            $this->redirect('dashboard');
        }
    }

    /**
     * عرض سجل التدقيق مع خيارات الفلترة
     */
    public function index() {
        $auditModel = $this->model('AuditLog');
        
        // معالجة الفلترة من GET
        $filterUser = isset($_GET['user']) ? (int) $_GET['user'] : null;
        $filterAction = isset($_GET['action']) ? trim($_GET['action']) : '';
        $filterTable = isset($_GET['table']) ? trim($_GET['table']) : '';
        
        $db = Database::getInstance();
        $sql = 'SELECT al.*, u.name as user_name 
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE 1=1';
        $params = [];
        
        if ($filterUser) {
            $sql .= ' AND al.user_id = :user';
            $params[':user'] = $filterUser;
        }
        if (!empty($filterAction)) {
            $sql .= ' AND al.action = :action';
            $params[':action'] = $filterAction;
        }
        if (!empty($filterTable)) {
            $sql .= ' AND al.table_name = :table';
            $params[':table'] = $filterTable;
        }
        
        $sql .= ' ORDER BY al.id DESC LIMIT 200';
        
        $db->query($sql);
        foreach ($params as $key => $value) {
            $db->bind($key, $value);
        }
        $logs = $db->resultSet();
        
        // جلب قوائم الفلترة (المستخدمين، الإجراءات، الجداول)
        $db->query('SELECT DISTINCT user_id, u.name FROM audit_logs al JOIN users u ON al.user_id = u.id');
        $users = $db->resultSet();
        
        $db->query('SELECT DISTINCT action FROM audit_logs');
        $actions = $db->resultSet();
        
        $db->query('SELECT DISTINCT table_name FROM audit_logs');
        $tables = $db->resultSet();
        
        $data = [
            'title' => 'سجل التدقيق',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'tables' => $tables,
            'filter_user' => $filterUser,
            'filter_action' => $filterAction,
            'filter_table' => $filterTable,
            'flash' => $this->getFlash()
        ];
        
        $this->view('audit/index', $data);
    }

    /**
     * عرض تفاصيل سجل معين (تم تغيير الاسم من view إلى show لحل التعارض)
     */
    public function show($id) {
        $db = Database::getInstance();
        $db->query('
            SELECT al.*, u.name as user_name
            FROM audit_logs al
            LEFT JOIN users u ON al.user_id = u.id
            WHERE al.id = :id
        ');
        $db->bind(':id', $id, PDO::PARAM_INT);
        $log = $db->single();
        
        if (!$log) {
            $this->setFlash('warning', 'السجل غير موجود');
            $this->redirect('audit/index');
        }
        
        // فك تشفير JSON (إن وجد)
        $log->old_data = $log->old_data ? json_decode($log->old_data, true) : null;
        $log->new_data = $log->new_data ? json_decode($log->new_data, true) : null;
        
        $data = [
            'title' => 'تفاصيل السجل',
            'log' => $log,
            'flash' => $this->getFlash()
        ];
        $this->view('audit/view', $data);
    }

    /**
     * حذف السجلات القديمة (تنظيف) - للصيانة
     */
    public function clean() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('audit/index');
        }
        
        $days = (int) ($_POST['days'] ?? 30);
        if ($days < 7) {
            $this->setFlash('error', 'الحد الأدنى هو 7 أيام');
            $this->redirect('audit/index');
        }
        
        $db = Database::getInstance();
        $db->query('DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)');
        $db->bind(':days', $days, PDO::PARAM_INT);
        $deleted = $db->execute() ? $db->rowCount() : 0;
        
        $this->setFlash('success', 'تم حذف ' . $deleted . ' سجل أقدم من ' . $days . ' يوم');
        $this->redirect('audit/index');
    }
}