<?php
// app/controllers/LeaveController.php

class LeaveController extends Controller {
    
    /** @var Leave */
    private Leave $leaveModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->leaveModel = $this->model('Leave');
    }

    /**
     * عرض قائمة طلبات الإجازات
     */
    public function index(): void {
        $requests = $this->leaveModel->getAllRequests();
        
        // التحقق مما إذا كان المستخدم مديراً لإظهار أزرار القبول/الرفض
        $isAdmin = Session::hasRole('admin');
        
        $data = [
            'title'    => 'إدارة الإجازات',
            'requests' => $requests,
            'is_admin' => $isAdmin,
            'flash'    => $this->getFlash()
        ];
        
        $this->view('leave/index', $data);
    }

    /**
     * تقديم طلب إجازة جديد
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id'   => (int)($_POST['employee_id'] ?? 0),
                'leave_type_id' => (int)($_POST['leave_type_id'] ?? 0),
                'start_date'    => trim($_POST['start_date'] ?? ''),
                'end_date'      => trim($_POST['end_date'] ?? ''),
                'reason'        => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['leave_type_id']) || empty($data['start_date']) || empty($data['end_date'])) {
                $this->setFlash('error', 'يرجى تعبئة كافة الحقول المطلوبة بشكل صحيح.');
                $this->redirect('leave/create');
            }

            // التحقق من صحة التواريخ (تاريخ النهاية يجب أن يكون بعد أو يساوي تاريخ البداية)
            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ نهاية الإجازة يجب أن يكون بعد تاريخ البداية.');
                $this->redirect('leave/create');
            }

            if ($this->leaveModel->createRequest($data)) {
                $this->setFlash('success', 'تم تقديم طلب الإجازة بنجاح، وهو قيد الانتظار لموافقة الإدارة.');
                $this->redirect('leave/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ الطلب.');
                $this->redirect('leave/create');
            }
        } else {
            // جلب الموظفين وأنواع الإجازات لعرضها في النموذج
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $leaveTypes = $this->leaveModel->getLeaveTypes();
            
            $data = [
                'title'       => 'تقديم طلب إجازة',
                'employees'   => $employees,
                'leave_types' => $leaveTypes,
                'flash'       => $this->getFlash()
            ];
            
            $this->view('leave/create', $data);
        }
    }

    /**
     * الموافقة على طلب الإجازة
     */
    public function approve(string $id = ''): void {
        $this->requireRole('admin'); // حماية: فقط الإدارة يمكنها الموافقة
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $requestId = (int)$id;
            $adminId = Session::getUserId();
            
            if ($this->leaveModel->updateStatus($requestId, 'approved', $adminId)) {
                $this->setFlash('success', 'تمت الموافقة على طلب الإجازة بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة الطلب.');
            }
        }
        $this->redirect('leave/index');
    }

    /**
     * رفض طلب الإجازة
     */
    public function reject(string $id = ''): void {
        $this->requireRole('admin'); // حماية: فقط الإدارة يمكنها الرفض
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $requestId = (int)$id;
            $adminId = Session::getUserId();
            
            if ($this->leaveModel->updateStatus($requestId, 'rejected', $adminId)) {
                $this->setFlash('success', 'تم رفض طلب الإجازة.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة الطلب.');
            }
        }
        $this->redirect('leave/index');
    }
}