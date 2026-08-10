<?php
// app/controllers/AttendanceController.php

class AttendanceController extends Controller {
    
    private $attendanceModel;

    public function __construct() {
        $this->requireAuth();
        $this->attendanceModel = $this->model('Attendance');
    }

    public function index() {
        $date = $_GET['date'] ?? date('Y-m-d');
        
        $records = $this->attendanceModel->getAttendanceByDate($date);
        
        // جلب قائمة الموظفين لتسجيل حضورهم
        $employeeModel = $this->model('Employee');
        $employees = [];
        if (method_exists($employeeModel, 'getAllEmployees')) {
            $employees = $employeeModel->getAllEmployees();
        } else {
            // Fallback to users table if Employee model is missing
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM users WHERE company_id = :cid");
            $db->bind(':cid', Session::get('company_id') ?: 1);
            $employees = $db->resultSet();
        }

        $data = [
            'title' => 'سجل الحضور والانصراف',
            'date' => $date,
            'records' => $records,
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الحضور والانصراف', 'url' => 'attendance/index']
            ]
        ];
        
        ob_start();
        $this->view('attendance/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function log() {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id' => (int)$_POST['employee_id'],
                'date' => trim($_POST['date']),
                'check_in' => trim($_POST['check_in'] ?? ''),
                'check_out' => trim($_POST['check_out'] ?? ''),
                'status' => trim($_POST['status'] ?? 'present'),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['date'])) {
                $this->setFlash('error', 'يجب تحديد الموظف والتاريخ.');
            } else {
                if ($this->attendanceModel->logAttendance($data)) {
                    $this->setFlash('success', 'تم حفظ السجل بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }
        $this->redirect('attendance/index?date=' . ($_POST['date'] ?? date('Y-m-d')));
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']);
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->attendanceModel->deleteAttendance((int)$id)) {
                $this->setFlash('success', 'تم حذف السجل بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف السجل.');
            }
        }
        $this->redirect('attendance/index');
    }
}