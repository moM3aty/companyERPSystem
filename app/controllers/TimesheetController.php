<?php
// app/controllers/TimesheetController.php

class TimesheetController extends Controller {
    
    private $timesheetModel;

    public function __construct() {
        $this->requireAuth();
        $this->timesheetModel = $this->model('Timesheet');
    }

    // 🟢 تعديل دالة index لتعرض السجل الشامل بدلاً من التحويل 🟢
    public function index() {
        $timesheets = $this->timesheetModel->getAllTimesheets();
        
        $data = [
            'title' => 'السجل الشامل لتتبع الوقت',
            'timesheets' => $timesheets,
            'breadcrumb' => [
                ['label' => 'المشاريع', 'url' => 'project/index'],
                ['label' => 'سجل الوقت الشامل', 'url' => 'timesheet/index']
            ]
        ];
        
        ob_start();
        $this->view('timesheet/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function project(string $projectId = '') {
        if (empty($projectId) || !is_numeric($projectId)) {
            $this->redirect('project/index');
        }
        
        $projectModel = $this->model('Project');
        $project = $projectModel->getProjectById((int)$projectId);
        
        if (!$project) {
            $this->setFlash('error', 'المشروع غير موجود.');
            $this->redirect('project/index');
        }

        $timesheets = $this->timesheetModel->getProjectTimesheets((int)$projectId);
        $tasks = $projectModel->getTasks((int)$projectId);
        
        $employeeModel = $this->model('Employee');
        $employees = $employeeModel->getAllEmployees();

        // حساب إجمالي الساعات
        $totalHours = 0;
        foreach ($timesheets as $ts) {
            $totalHours += (float)$ts->total_hours;
        }

        $data = [
            'title' => 'سجل تتبع الوقت للمشروع',
            'project' => $project,
            'timesheets' => $timesheets,
            'tasks' => $tasks,
            'employees' => $employees,
            'totalHours' => $totalHours,
            'breadcrumb' => [
                ['label' => 'المشاريع', 'url' => 'project/index'],
                ['label' => 'المهام', 'url' => 'project/show/' . $projectId],
                ['label' => 'تتبع الوقت', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('project/timesheets', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function logTime(string $projectId = '') {
        if ($this->isPost() && !empty($projectId)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $start = strtotime($_POST['start_time']);
            $end = strtotime($_POST['end_time']);
            
            // في حالة كان الإنتهاء بعد منتصف الليل
            if ($end < $start) {
                $end += 86400; // إضافة 24 ساعة
            }
            
            // حساب الفارق بالساعات (التقريب لمنزلتين عشريتين)
            $totalHours = round(($end - $start) / 3600, 2);

            $data = [
                'project_id' => (int)$projectId,
                'task_id' => !empty($_POST['task_id']) ? (int)$_POST['task_id'] : null,
                'employee_id' => (int)$_POST['employee_id'],
                'date' => $_POST['date'],
                'start_time' => $_POST['start_time'],
                'end_time' => $_POST['end_time'],
                'total_hours' => $totalHours,
                'note' => trim($_POST['note'] ?? '')
            ];

            if ($this->timesheetModel->logTime($data)) {
                $this->setFlash('success', 'تم حفظ وقت الإنجاز بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }
        $this->redirect('timesheet/project/' . $projectId);
    }
}