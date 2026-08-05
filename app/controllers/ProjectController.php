<?php
class ProjectController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $db = Database::getInstance();
        $db->query('
            SELECT p.*, c.name as customer_name, e.name as manager_name
            FROM projects p
            LEFT JOIN customers c ON p.customer_id = c.id
            LEFT JOIN employees e ON p.project_manager = e.id
            ORDER BY p.id DESC
        ');
        $projects = $db->resultSet();
        
        $data = [
            'title' => 'المشاريع',
            'projects' => $projects,
            'flash' => $this->getFlash()
        ];
        $this->view('project/index', $data);
    }

    public function view($id) {
        // عرض تفاصيل المشروع مع المهام
        $db = Database::getInstance();
        $db->query('
            SELECT p.*, c.name as customer_name, e.name as manager_name
            FROM projects p
            LEFT JOIN customers c ON p.customer_id = c.id
            LEFT JOIN employees e ON p.project_manager = e.id
            WHERE p.id = :id
        ');
        $db->bind(':id', $id, PDO::PARAM_INT);
        $project = $db->single();
        
        if (!$project) {
            $this->setFlash('warning', 'المشروع غير موجود');
            $this->redirect('project/index');
        }
        
        // جلب المهام
        $db->query('SELECT * FROM project_tasks WHERE project_id = :pid ORDER BY due_date');
        $db->bind(':pid', $id, PDO::PARAM_INT);
        $tasks = $db->resultSet();
        
        $data = [
            'title' => 'تفاصيل المشروع',
            'project' => $project,
            'tasks' => $tasks,
            'flash' => $this->getFlash()
        ];
        $this->view('project/view', $data);
    }

    public function create() {
        // مشابه لباقي عمليات الإضافة
        // ...
    }

    public function addTask() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // إضافة مهمة جديدة
        }
    }
}