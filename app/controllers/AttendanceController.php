<?php
// app/controllers/AttendanceController.php

class AttendanceController extends Controller {
    
    private Attendance $attendanceModel;

    public function __construct() {
        $this->requireAuth();
        $this->attendanceModel = $this->model('Attendance');
    }

    /**
     * عرض السجل اليومي للحضور والانصراف
     */
    public function index(): void {
        // جلب التاريخ من الرابط، وإلا استخدم تاريخ اليوم
        $date = $this->getQuery('date', date('Y-m-d'));
        
        $records = $this->attendanceModel->getDailyAttendance($date);
        
        $data = [
            'title' => 'الحضور والانصراف',
            'records' => $records,
            'current_date' => $date,
            'flash' => $this->getFlash()
        ];
        
        $this->view('attendance/index', $data);
    }

    /**
     * تسجيل حالة حضور لموظف
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id' => !empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0,
                'date'        => trim($_POST['date'] ?? date('Y-m-d')),
                'check_in'    => trim($_POST['check_in'] ?? ''),
                'check_out'   => trim($_POST['check_out'] ?? ''),
                'status'      => trim($_POST['status'] ?? 'present'),
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            if ($data['employee_id'] === 0) {
                $this->setFlash('error', 'الرجاء تحديد الموظف المطلوب.');
                $this->redirect('attendance/create');
            }

            if ($this->attendanceModel->recordAttendance($data)) {
                $this->setFlash('success', 'تم حفظ حالة الحضور للموظف بنجاح.');
                $this->redirect('attendance/index?date=' . $data['date']);
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء تسجيل الحضور.');
                $this->redirect('attendance/create');
            }
        } else {
            // جلب الموظفين لعرضهم في النموذج
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'تسجيل حضور / انصراف',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            
            $this->view('attendance/create', $data);
        }
    }
}