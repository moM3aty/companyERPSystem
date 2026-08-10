<?php
// app/controllers/LeaveController.php

class LeaveController extends Controller {
    
    private $leaveModel;

    public function __construct() {
        $this->requireAuth();
        $this->leaveModel = $this->model('Leave');
    }

    public function index() {
        $userId = Session::getUserId();
        $userRole = Session::getUserRole();
        
        // جلب الإجازات بناءً على الصلاحية
        if (in_array($userRole, ['admin', 'manager', 'super_admin'])) {
            $leaves = $this->leaveModel->getAllLeaves();
        } else {
            $leaves = $this->leaveModel->getEmployeeLeaves($userId);
        }
        
        $data = [
            'title' => 'طلبات الإجازات',
            'leaves' => $leaves,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الإجازات', 'url' => 'leave/index']
            ]
        ];
        
        ob_start();
        $this->view('leave/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id' => Session::getUserId(), 
                'leave_type' => trim($_POST['leave_type'] ?? 'annual'),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'reason' => trim($_POST['reason'] ?? '')
            ];

            // إذا كان المدير هو من يسجل الإجازة لموظف آخر
            if (in_array(Session::getUserRole(), ['admin', 'manager', 'super_admin']) && !empty($_POST['employee_id'])) {
                $data['employee_id'] = (int)$_POST['employee_id'];
            }

            if (empty($data['start_date']) || empty($data['end_date'])) {
                $this->setFlash('error', 'يرجى تحديد تواريخ بداية ونهاية الإجازة.');
            } else {
                $start = strtotime($data['start_date']);
                $end = strtotime($data['end_date']);
                $days = round(($end - $start) / (60 * 60 * 24)) + 1;
                $data['days'] = $days;

                if ($this->leaveModel->createLeave($data)) {
                    $this->setFlash('success', 'تم تقديم طلب الإجازة بنجاح.');
                    $this->redirect('leave/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تقديم الطلب.');
                }
            }
        }

        $employees = [];
        if (in_array(Session::getUserRole(), ['admin', 'manager', 'super_admin'])) {
            $employeeModel = $this->model('Employee');
            if (method_exists($employeeModel, 'getAllEmployees')) {
                $employees = $employeeModel->getAllEmployees();
            }
        }

        $data = [
            'title' => 'تقديم طلب إجازة',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الإجازات', 'url' => 'leave/index'],
                ['label' => 'طلب جديد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('leave/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function updateStatus($id = '') {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);
        
        if (empty($id) || !is_numeric($id)) $this->redirect('leave/index');

        if ($this->isPost()) {
            $status = trim($_POST['status'] ?? '');
            if (in_array($status, ['approved', 'rejected'])) {
                if ($this->leaveModel->updateStatus((int)$id, $status)) {
                    $this->setFlash('success', 'تم تحديث حالة الطلب.');
                } else {
                    $this->setFlash('error', 'فشل في تحديث الحالة.');
                }
            }
        }
        $this->redirect('leave/index');
    }

    public function delete($id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $leaveId = (int)$id;
            $leave = $this->leaveModel->getLeaveById($leaveId);

            if ($leave) {
                // يُسمح بالحذف إذا كان الطلب للمستخدم وما زال معلقاً، أو إذا كان المستخدم مديراً
                if (in_array(Session::getUserRole(), ['admin', 'super_admin']) || ($leave->employee_id == Session::getUserId() && $leave->status == 'pending')) {
                    if ($this->leaveModel->deleteLeave($leaveId)) {
                        $this->setFlash('success', 'تم إلغاء طلب الإجازة.');
                    } else {
                        $this->setFlash('error', 'حدث خطأ أثناء الإلغاء.');
                    }
                } else {
                    $this->setFlash('error', 'لا يمكنك حذف هذا الطلب.');
                }
            }
        }
        $this->redirect('leave/index');
    }
}