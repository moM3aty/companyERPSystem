<?php
// app/controllers/ProjectController.php

class ProjectController extends Controller {
    
    private $projectModel;

    public function __construct() {
        $this->requireAuth();
        $this->projectModel = $this->model('Project');
    }

    public function index() {
        $projects = $this->projectModel->getAllProjects();
        $data = [
            'title' => 'إدارة المشاريع',
            'projects' => $projects,
            'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index']]
        ];
        
        ob_start(); $this->view('project/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'budget' => (float)($_POST['budget'] ?? 0),
                'status' => trim($_POST['status'] ?? 'active')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يرجى إدخال اسم المشروع.');
            } elseif ($this->projectModel->createProject($data)) {
                $this->setFlash('success', 'تم إنشاء المشروع بنجاح.');
                $this->redirect('project/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإنشاء.');
            }
        }
        $data = ['title' => 'إضافة مشروع', 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'إضافة', 'url' => '#']]];
        ob_start(); $this->view('project/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('project/index');
        
        $project = $this->projectModel->getProjectById((int)$id);
        if (!$project) {
            $this->setFlash('error', 'المشروع غير موجود.');
            $this->redirect('project/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'budget' => (float)($_POST['budget'] ?? 0),
                'status' => trim($_POST['status'] ?? 'active')
            ];

            if ($this->projectModel->updateProject((int)$id, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات المشروع بنجاح.');
                $this->redirect('project/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
            }
        }

        $data = ['title' => 'تعديل مشروع', 'project' => $project, 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'تعديل', 'url' => '#']]];
        ob_start(); $this->view('project/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('project/index');
        $project = $this->projectModel->getProjectById((int)$id);
        if (!$project) $this->redirect('project/index');

        $tasks = $this->projectModel->getTasks((int)$id);
        $employeeModel = $this->model('Employee');
        $employees = $employeeModel->getAllEmployees();

        $data = [
            'title' => 'لوحة المشروع والمهام',
            'project' => $project,
            'tasks' => $tasks,
            'employees' => $employees,
            'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'التفاصيل', 'url' => '#']]
        ];
        
        ob_start(); $this->view('project/view', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function addTask(string $projectId = '') {
        if ($this->isPost() && !empty($projectId)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'project_id' => (int)$projectId,
                'title' => trim($_POST['title']),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'start_date' => $_POST['start_date'],
                'due_date' => $_POST['due_date']
            ];
            $this->projectModel->addTask($data);
            $this->setFlash('success', 'تم إضافة المهمة بنجاح.');
        }
        $this->redirect('project/show/' . $projectId);
    }

    public function updateTaskProgress(string $taskId = '') {
        if ($this->isPost() && !empty($taskId)) {
            $progress = (int)($_POST['progress'] ?? 0);
            $projectId = $_POST['project_id'] ?? '';
            $this->projectModel->updateTaskProgress((int)$taskId, $progress);
            $this->setFlash('success', 'تم تحديث نسبة الإنجاز.');
            $this->redirect('project/show/' . $projectId);
        } else {
            $this->redirect('project/index');
        }
    }

    public function delete(string $id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->projectModel->deleteProject((int)$id);
            $this->setFlash('success', 'تم حذف المشروع ومهامه بنجاح.');
        }
        $this->redirect('project/index');
    }
}