<?php
// app/controllers/AttendanceController.php

class AttendanceController extends Controller {
    
    private $attendanceModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->attendanceModel = $this->model('Attendance');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        // نستخدم $_GET مباشرة بدلاً من دالة getQuery المفقودة
        $filterDate = $_GET['date'] ?? date('Y-m-d');
        
        $attendance = $this->attendanceModel->getAllAttendance($filterDate);
        
        $data = [
            'title' => 'الحضور والانصراف',
            'attendance' => $attendance,
            'filter_date' => $filterDate,
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

    public function create() {
        if ($this->isPost()) {
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'date' => trim($_POST['date'] ?? date('Y-m-d')),
                'check_in' => trim($_POST['check_in'] ?? ''),
                'check_out' => trim($_POST['check_out'] ?? ''),
                'status' => trim($_POST['status'] ?? 'present'),
                'notes' => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            if (empty($data['employee_id']) || empty($data['date'])) {
                $this->setFlash('error', 'يجب تحديد الموظف والتاريخ.');
            } elseif ($this->attendanceModel->checkExists($data['employee_id'], $data['date'])) {
                $this->setFlash('error', 'يوجد سجل حضور مسبق لهذا الموظف في نفس اليوم.');
            } else {
                if ($this->attendanceModel->addAttendance($data)) {
                    $this->setFlash('success', 'تم تسجيل الحضور بنجاح.');
                    $this->redirect('attendance/index?date=' . $data['date']);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        $employees = $this->employeeModel->getAllEmployees();

        $data = [
            'title' => 'تسجيل حضور جديد',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الحضور والانصراف', 'url' => 'attendance/index'],
                ['label' => 'تسجيل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('attendance/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('attendance/index');
        
        $attId = (int)$id;
        $attendance = $this->attendanceModel->getAttendanceById($attId);
        
        if (!$attendance) {
            $this->setFlash('error', 'السجل غير موجود.');
            $this->redirect('attendance/index');
        }

        if ($this->isPost()) {
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'date' => trim($_POST['date'] ?? ''),
                'check_in' => trim($_POST['check_in'] ?? ''),
                'check_out' => trim($_POST['check_out'] ?? ''),
                'status' => trim($_POST['status'] ?? 'present'),
                'notes' => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            if (empty($data['employee_id']) || empty($data['date'])) {
                $this->setFlash('error', 'يجب تحديد الموظف والتاريخ.');
            } elseif ($this->attendanceModel->checkExists($data['employee_id'], $data['date'], $attId)) {
                $this->setFlash('error', 'يوجد سجل آخر لهذا الموظف في نفس اليوم.');
            } else {
                if ($this->attendanceModel->updateAttendance($attId, $data)) {
                    $this->setFlash('success', 'تم تعديل السجل بنجاح.');
                    $this->redirect('attendance/index?date=' . $data['date']);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                }
            }
        }

        $employees = $this->employeeModel->getAllEmployees();

        $data = [
            'title' => 'تعديل سجل الحضور',
            'attendance' => $attendance,
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الحضور والانصراف', 'url' => 'attendance/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('attendance/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->attendanceModel->deleteAttendance((int)$id)) {
                $this->setFlash('success', 'تم حذف السجل بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('attendance/index');
    }
}