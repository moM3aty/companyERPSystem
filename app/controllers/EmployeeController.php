<?php
// المسار: app/controllers/EmployeeController.php

class EmployeeController extends Controller {
    
    private Employee $empModel;

    public function __construct() {
        // حماية الوصول: يجب أن يكون مسجلاً للدخول
        $this->requireAuth();
        $this->empModel = $this->model('Employee');
    }

    public function index(): void {
        $employees = $this->empModel->getAllEmployees();
        
        $data = [
            'title' => 'إدارة الموظفين (HR)',
            'employees' => $employees,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'الموظفين', 'url' => 'employee/index']]
        ];
        
        // تمرير المحتوى للـ Layout ليتولى التغليف
        ob_start();
        $this->view('employee/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    public function create(): void {
        // التحقق من الصلاحيات (فقط الإدارة والـ HR من يحق لهم الإضافة)
        $this->requireAnyRole(['admin', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'email'         => trim($_POST['email'] ?? ''),
                'phone'         => trim($_POST['phone'] ?? ''),
                'position'      => trim($_POST['position'] ?? ''),
                'salary'        => (float)($_POST['salary'] ?? 0.00),
                'department_id' => !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null
            ];

            // التحقق من البيانات الأساسية
            if (empty($data['name']) || empty($data['email']) || empty($data['position'])) {
                $this->setFlash('error', 'يرجى تعبئة جميع الحقول المطلوبة (الاسم، البريد، المسمى الوظيفي).');
                $this->redirect('employee/create');
            }

            // التأكد من عدم تكرار البريد
            if ($this->empModel->emailExists($data['email'])) {
                $this->setFlash('error', 'البريد الإلكتروني مسجل مسبقاً لموظف آخر.');
                $this->redirect('employee/create');
            }

            if ($this->empModel->create($data)) {
                $this->setFlash('success', 'تم تسجيل بيانات الموظف بنجاح.');
                $this->redirect('employee/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء الحفظ.');
                $this->redirect('employee/create');
            }
        } else {
            // جلب الأقسام لعرضها في النموذج
            $departments = $this->empModel->getDepartments();
            
            $data = [
                'title' => 'تسجيل موظف جديد',
                'departments' => $departments,
                'breadcrumb' => [
                    ['label' => 'الموارد البشرية', 'url' => '#'],
                    ['label' => 'الموظفين', 'url' => 'employee/index'],
                    ['label' => 'إضافة موظف', 'url' => 'employee/create']
                ]
            ];
            
            ob_start();
            $this->view('employee/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin'); // فقط المدير العام يمكنه الحذف
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->empModel->delete((int)$id)) {
                    $this->setFlash('success', 'تم حذف الموظف من سجلات النظام بنجاح.');
                } else {
                    $this->setFlash('error', 'فشل في حذف بيانات الموظف.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف الموظف لوجود حركات مالية، رواتب، أو عهد مرتبطة به.');
            }
        }
        $this->redirect('employee/index');
    }
}