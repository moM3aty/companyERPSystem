<?php
// المسار: app/controllers/TimesheetController.php

class TimesheetController extends Controller {
    private Timesheet $timeModel;
    private Project $projectModel;

    public function __construct() {
        $this->requireAuth();
        $this->timeModel = $this->model('Timesheet');
        $this->projectModel = $this->model('Project');
    }

    public function project(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('project/index');
        $projectId = (int)$id;
        
        $project = $this->projectModel->getProjectById($projectId);
        if (!$project) $this->redirect('project/index');
        
        $timesheets = $this->timeModel->getTimesheetsByProject($projectId);
        $totalHours = $this->timeModel->getTotalHoursForProject($projectId);
        $tasks = $this->projectModel->getProjectTasks($projectId);
        
        $db = Database::getInstance();
        $db->query("SELECT id, name FROM employees ORDER BY name ASC");
        $employees = $db->resultSet();

        $data = [
            'title' => 'تتبع وقت المشروع',
            'project' => $project,
            'timesheets' => $timesheets,
            'totalHours' => $totalHours,
            'tasks' => $tasks,
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'المشاريع', 'url' => 'project/index'],
                ['label' => 'تتبع الوقت', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('project/timesheets', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function logTime(string $projectId = ''): void {
        if ($this->isPost() && !empty($projectId) && is_numeric($projectId)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $startTime = trim($_POST['start_time'] ?? '');
            $endTime = trim($_POST['end_time'] ?? '');
            
            $t1 = strtotime($startTime);
            $t2 = strtotime($endTime);
            $diffHours = ($t2 - $t1) / 3600;
            if($diffHours < 0) $diffHours = 0;

            $data = [
                'project_id' => (int)$projectId,
                'task_id' => !empty($_POST['task_id']) ? (int)$_POST['task_id'] : null,
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'date' => trim($_POST['date'] ?? date('Y-m-d')),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'total_hours' => round($diffHours, 2),
                'note' => trim($_POST['note'] ?? '')
            ];

            if (empty($data['employee_id']) || $diffHours <= 0) {
                $this->setFlash('error', 'بيانات الوقت غير صحيحة، تأكد من إدخال وقت صحيح.');
            } else {
                if ($this->timeModel->logTime($data)) {
                    $this->setFlash('success', 'تم تسجيل الوقت بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التسجيل.');
                }
            }
        }
        $this->redirect('timesheet/project/' . $projectId);
    }
}