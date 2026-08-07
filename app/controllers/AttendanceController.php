<?php
// app/controllers/AttendanceController.php

class AttendanceController extends Controller {
    
    private Attendance $attendanceModel;

    public function __construct() {
        $this->requireAuth();
        $this->attendanceModel = $this->model('Attendance');
    }

    public function index(): void {
        $date = $this->getQuery('date', date('Y-m-d'));
        $records = $this->attendanceModel->getDailyAttendance($date);
        
        $data = [
            'title' => 'سجل الحضور والانصراف',
            'records' => $records,
            'current_date' => $date,
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

    public function create(): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
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

            if (in_array($data['status'], ['absent', 'leave'])) {
                $data['check_in'] = null;
                $data['check_out'] = null;
            }

            if ($this->attendanceModel->createAttendance($data)) {
                $this->setFlash('success', 'تم تسجيل حضور الموظف بنجاح.');
                $this->redirect('attendance/index?date=' . $data['date']);
            } else {
                $this->setFlash('error', 'تم تسجيل حضور لهذا الموظف مسبقاً في هذا اليوم.');
                $this->redirect('attendance/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'تسجيل حضور / انصراف',
                'employees' => $employees,
                'breadcrumb' => [
                    ['label' => 'الحضور والانصراف', 'url' => 'attendance/index'],
                    ['label' => 'تسجيل حالة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('attendance/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if (empty($id) || !is_numeric($id)) $this->redirect('attendance/index');

        $recordId = (int)$id;
        $record = $this->attendanceModel->getAttendanceById($recordId);

        if (!$record) {
            $this->setFlash('error', 'السجل المطلوب غير موجود.');
            $this->redirect('attendance/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? $record->employee_id),
                'date'        => trim($_POST['date'] ?? $record->date),
                'check_in'    => trim($_POST['check_in'] ?? ''),
                'check_out'   => trim($_POST['check_out'] ?? ''),
                'status'      => trim($_POST['status'] ?? 'present'),
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            if (in_array($data['status'], ['absent', 'leave'])) {
                $data['check_in'] = null;
                $data['check_out'] = null;
            }

            if ($this->attendanceModel->updateAttendance($recordId, $data)) {
                $this->setFlash('success', 'تم تعديل السجل بنجاح.');
                $this->redirect('attendance/index?date=' . $data['date']);
            } else {
                $this->setFlash('error', 'حدث خطأ، ربما يوجد سجل آخر لهذا الموظف في نفس اليوم.');
                $this->redirect('attendance/edit/' . $recordId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'تعديل سجل حضور',
                'record' => $record,
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
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->attendanceModel->deleteAttendance((int)$id)) {
                $this->setFlash('success', 'تم حذف سجل الحضور.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('attendance/index');
    }
}