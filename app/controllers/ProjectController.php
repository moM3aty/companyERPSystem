<?php
// app/controllers/ProjectController.php
class ProjectController extends Controller {
    private Project $projectModel;
    public function __construct() {
        $this->requireAuth();
        $this->projectModel = $this->model('Project');
    }
    public function index(): void {
        $projects = $this->projectModel->getAllProjects();
        $data = ['title' => 'المشاريع والمهام', 'projects' => $projects, 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index']]];
        ob_start();
        $this->view('project/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'code'            => trim($_POST['code'] ?? ''),
                'customer_id'     => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'project_manager' => !empty($_POST['project_manager']) ? (int)$_POST['project_manager'] : null,
                'start_date'      => trim($_POST['start_date'] ?? ''),
                'end_date'        => trim($_POST['end_date'] ?? ''),
                'budget'          => (float)($_POST['budget'] ?? 0.0),
                'status'          => trim($_POST['status'] ?? 'planning'),
                'description'     => trim($_POST['description'] ?? '')
            ];
            if (empty($data['name']) || empty($data['code'])) {
                $this->setFlash('error', 'يرجى إدخال اسم المشروع والكود التعريفي.');
                $this->redirect('project/create');
            }
            if ($this->projectModel->createProject($data)) {
                $this->setFlash('success', 'تم إنشاء المشروع بنجاح.');
                $this->redirect('project/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ المشروع.');
                $this->redirect('project/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            $data = ['title' => 'إضافة مشروع جديد', 'customers' => $customers, 'employees' => $employees, 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'إضافة', 'url' => '#']]];
            ob_start();
            $this->view('project/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        if (empty($id) || !is_numeric($id)) $this->redirect('project/index');
        $projectId = (int)$id;
        $project = $this->projectModel->getProjectById($projectId);
        if (!$project) {
            $this->setFlash('error', 'المشروع غير موجود.');
            $this->redirect('project/index');
        }
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'code'            => trim($_POST['code'] ?? ''),
                'customer_id'     => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'project_manager' => !empty($_POST['project_manager']) ? (int)$_POST['project_manager'] : null,
                'start_date'      => trim($_POST['start_date'] ?? ''),
                'end_date'        => trim($_POST['end_date'] ?? ''),
                'budget'          => (float)($_POST['budget'] ?? 0.0),
                'status'          => trim($_POST['status'] ?? 'planning'),
                'description'     => trim($_POST['description'] ?? '')
            ];
            if ($this->projectModel->updateProject($projectId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات المشروع بنجاح.');
                $this->redirect('project/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('project/edit/' . $projectId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            $data = ['title' => 'تعديل المشروع', 'project' => $project, 'customers' => $customers, 'employees' => $employees, 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'تعديل', 'url' => '#']]];
            ob_start();
            $this->view('project/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('project/index');
        $projectId = (int)$id;
        $project = $this->projectModel->getProjectById($projectId);
        if (!$project) {
            $this->setFlash('error', 'المشروع المطلوب غير موجود.');
            $this->redirect('project/index');
        }
        $tasks = $this->projectModel->getProjectTasks($projectId);
        $db = Database::getInstance();
        $db->query("SELECT id, name FROM employees ORDER BY name ASC");
        $employees = $db->resultSet();
        $data = ['title' => 'تفاصيل ومهام المشروع', 'project' => $project, 'tasks' => $tasks, 'employees' => $employees, 'breadcrumb' => [['label' => 'المشاريع', 'url' => 'project/index'], ['label' => 'تفاصيل', 'url' => '#']]];
        ob_start();
        $this->view('project/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    public function addTask(string $projectId = ''): void {
       if ($this->isPost() && !empty($projectId) && is_numeric($projectId)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'project_id'  => (int)$projectId,
                'title'       => trim($_POST['title'] ?? ''),
                'start_date'  => trim($_POST['start_date'] ?? ''),
                'due_date'    => trim($_POST['due_date'] ?? ''),
                'progress'    => (int)($_POST['progress'] ?? 0),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null
            ];
            if (empty($data['title']) || empty($data['start_date']) || empty($data['due_date'])) {
                $this->setFlash('error', 'يجب إدخال عنوان المهمة والتواريخ.');
            } else {
                if ($this->projectModel->createTask($data)) {
                    $this->setFlash('success', 'تم إضافة المهمة بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الإضافة.');
                }
            }
        }
        $this->redirect('project/show/' . $projectId);
    }
        public function updateTaskProgress(string $taskId = ''): void {
        if ($this->isPost() && !empty($taskId) && is_numeric($taskId)) {
            $progress = (int)($_POST['progress'] ?? 0);
            $projectId = (int)($_POST['project_id'] ?? 0);
            
            if ($progress >= 0 && $progress <= 100) {
                if ($this->projectModel->updateTaskProgress((int)$taskId, $progress)) {
                    $this->setFlash('success', 'تم تحديث نسبة إنجاز المهمة بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تحديث المهمة.');
                }
            }
            
            if ($projectId > 0) {
                $this->redirect('project/show/' . $projectId);
            } else {
                $this->redirect('project/index');
            }
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->projectModel->deleteProject((int)$id)) {
                $this->setFlash('success', 'تم حذف المشروع بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف المشروع.');
            }
        }
        $this->redirect('project/index');
    }
}