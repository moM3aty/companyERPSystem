<?php
// app/controllers/ProjectController.php

class ProjectController extends Controller {
    
    /** @var Project */
    private Project $projectModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->projectModel = $this->model('Project');
    }

    public function index(): void {
        $projects = $this->projectModel->getAllProjects();
        
        $data = [
            'title' => 'المشاريع والمهام',
            'projects' => $projects,
            'flash' => $this->getFlash()
        ];
        
        $this->view('project/index', $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'code'            => trim($_POST['code'] ?? ''),
                'customer_id'     => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'project_manager' => !empty($_POST['project_manager']) ? (int)$_POST['project_manager'] : null,
                'start_date'      => !empty($_POST['start_date']) ? trim($_POST['start_date']) : null,
                'end_date'        => !empty($_POST['end_date']) ? trim($_POST['end_date']) : null,
                'budget'          => (float)($_POST['budget'] ?? 0.00),
                'status'          => trim($_POST['status'] ?? 'planning'),
                'description'     => trim($_POST['description'] ?? '')
            ];

            if (empty($data['name']) || empty($data['code'])) {
                $this->setFlash('error', 'يرجى إدخال اسم المشروع والكود التعريفي.');
                $this->redirect('project/create');
            }

            if ($this->projectModel->createProject($data)) {
                $this->setFlash('success', 'تم تسجيل المشروع الجديد بنجاح.');
                $this->redirect('project/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات المشروع.');
                $this->redirect('project/create');
            }
            
        } else {
            // جلب العملاء والموظفين لعرضهم في قوائم الاختيار
            $db = Database::getInstance();
            
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();

            $data = [
                'title'     => 'إضافة مشروع جديد',
                'customers' => $customers,
                'employees' => $employees,
                'flash'     => $this->getFlash()
            ];
            
            $this->view('project/create', $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('project/index');
        }

        $projectId = (int)$id;
        $project = $this->projectModel->getProjectById($projectId);
        
        if (!$project) {
            $this->setFlash('error', 'المشروع المطلوب غير موجود.');
            $this->redirect('project/index');
        }

        // جلب مهام المشروع
        $tasks = $this->projectModel->getProjectTasks($projectId);

        $data = [
            'title'   => 'تفاصيل المشروع',
            'project' => $project,
            'tasks'   => $tasks,
            'flash'   => $this->getFlash()
        ];

        $this->view('project/view', $data);
    }

    /**
     * إضافة مهمة جديدة داخل المشروع
     */
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
                $this->setFlash('error', 'يرجى إدخال عنوان المهمة وتاريخي البداية والنهاية.');
            } else if (strtotime($data['due_date']) < strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ نهاية المهمة يجب أن يكون بعد تاريخ البداية.');
            } else {
                if ($this->projectModel->createTask($data)) {
                    $this->setFlash('success', 'تم إضافة المهمة وتحديث مخطط جانت بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء إضافة المهمة.');
                }
            }
        }
        $this->redirect('project/show/' . $projectId);
    }
}