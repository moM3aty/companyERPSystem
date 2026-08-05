<?php
class LeaveController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $leaveModel = $this->model('Leave');
        $employeeModel = $this->model('Employee');
        
        // إذا كان المستخدم admin أو مدير، يعرض كل الطلبات، وإلا يعرض طلباته فقط
        $isAdmin = ($_SESSION['user_role'] === 'admin');
        
        if ($isAdmin) {
            // جلب كل الطلبات مع بيانات الموظفين
            $db = Database::getInstance();
            $db->query('
                SELECT lr.*, e.name as employee_name, lt.name as leave_type_name
                FROM leave_requests lr
                JOIN employees e ON lr.employee_id = e.id
                LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
                ORDER BY lr.id DESC
            ');
            $requests = $db->resultSet();
        } else {
            // جلب طلبات الموظف الحالي (نفترض أن لدينا employee_id مرتبط بـ user_id)
            // يمكن ربط users مع employees، لكن هنا نفترض أن user_id = employee_id أو لدينا جدول user_employee
            // سنفترض أن session تحوي employee_id
            $employeeId = $_SESSION['employee_id'] ?? 0;
            $requests = $leaveModel->getByEmployee($employeeId);
        }
        
        $data = [
            'title' => 'إدارة الإجازات',
            'requests' => $requests,
            'is_admin' => $isAdmin,
            'flash' => $this->getFlash()
        ];
        
        $this->view('leave/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $employeeId = $_SESSION['employee_id'] ?? 0;
            $leaveTypeId = (int) $_POST['leave_type_id'];
            $start = $_POST['start_date'];
            $end = $_POST['end_date'];
            $reason = trim($_POST['reason']);
            
            $errors = [];
            if (empty($employeeId)) $errors[] = 'معرّف الموظف غير موجود';
            if ($leaveTypeId <= 0) $errors[] = 'نوع الإجازة مطلوب';
            if (empty($start) || empty($end)) $errors[] = 'تاريخ البداية والنهاية مطلوبان';
            if (strtotime($end) < strtotime($start)) $errors[] = 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية';
            
            if (empty($errors)) {
                $leaveModel = $this->model('Leave');
                if ($leaveModel->request($employeeId, $leaveTypeId, $start, $end, $reason)) {
                    $this->setFlash('success', 'تم تقديم طلب الإجازة بنجاح');
                    // إرسال إشعار للمدير (اختياري)
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تقديم الطلب');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }
            
            $this->redirect('leave/index');
        } else {
            // عرض نموذج الإضافة
            $leaveModel = $this->model('Leave');
            // جلب أنواع الإجازات
            $db = Database::getInstance();
            $db->query('SELECT * FROM leave_types');
            $leaveTypes = $db->resultSet();
            
            $data = [
                'title' => 'طلب إجازة جديد',
                'leave_types' => $leaveTypes,
                'flash' => $this->getFlash()
            ];
            
            $this->view('leave/create', $data);
        }
    }

    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('leave/index');
        }
        
        $leaveModel = $this->model('Leave');
        if ($leaveModel->approve($id, $_SESSION['user_id'])) {
            $this->setFlash('success', 'تمت الموافقة على طلب الإجازة');
        } else {
            $this->setFlash('error', 'حدث خطأ أثناء الموافقة');
        }
        
        $this->redirect('leave/index');
    }

    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('leave/index');
        }
        
        $db = Database::getInstance();
        $db->query('UPDATE leave_requests SET status = "rejected" WHERE id = :id');
        $db->bind(':id', $id, PDO::PARAM_INT);
        if ($db->execute()) {
            $this->setFlash('success', 'تم رفض طلب الإجازة');
        } else {
            $this->setFlash('error', 'حدث خطأ أثناء الرفض');
        }
        
        $this->redirect('leave/index');
    }
}