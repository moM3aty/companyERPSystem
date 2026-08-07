<?php
// app/controllers/EmployeeController.php

class EmployeeController extends Controller {
    
    private Employee $empModel;

    public function __construct() {
        $this->requireAuth();
        $this->empModel = $this->model('Employee');
    }

    public function index(): void {
        $employees = $this->empModel->getAllEmployees();
        $data = [
            'title' => 'دليل الموظفين (HR)', 
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الموظفين', 'url' => 'employee/index']
            ]
        ];
        
        ob_start();
        $this->view('employee/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'      => trim($_POST['name'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'phone'     => trim($_POST['phone'] ?? ''),
                'position'  => trim($_POST['position'] ?? ''),
                'salary'    => (float)($_POST['salary'] ?? 0.00),
                'hire_date' => trim($_POST['hire_date'] ?? date('Y-m-d'))
            ];

            if (empty($data['name']) || empty($data['position'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول المطلوبة (الاسم، المسمى الوظيفي).');
                $this->redirect('employee/create');
            }

            if ($this->empModel->emailExists($data['email'])) {
                $this->setFlash('error', 'البريد الإلكتروني مسجل مسبقاً لموظف آخر.');
                $this->redirect('employee/create');
            }

            if ($this->empModel->createEmployee($data)) {
                $this->setFlash('success', 'تم تسجيل بيانات الموظف بنجاح.');
                $this->redirect('employee/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء الحفظ.');
                $this->redirect('employee/create');
            }
        } else {
            $data = [
                'title' => 'تسجيل موظف جديد',
                'breadcrumb' => [
                    ['label' => 'الموظفين', 'url' => 'employee/index'],
                    ['label' => 'إضافة موظف', 'url' => '#']
                ]
            ];
            ob_start();
            $this->view('employee/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        
        if (empty($id) || !is_numeric($id)) $this->redirect('employee/index');
        
        $empId = (int)$id;
        $employee = $this->empModel->findById($empId);

        if (!$employee) {
            $this->setFlash('error', 'الموظف غير موجود.');
            $this->redirect('employee/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'      => trim($_POST['name'] ?? ''),
                'email'     => trim($_POST['email'] ?? ''),
                'phone'     => trim($_POST['phone'] ?? ''),
                'position'  => trim($_POST['position'] ?? ''),
                'salary'    => (float)($_POST['salary'] ?? 0.00),
                'hire_date' => trim($_POST['hire_date'] ?? $employee->hire_date)
            ];

            if (empty($data['name']) || empty($data['position'])) {
                $this->setFlash('error', 'الاسم والمسمى الوظيفي مطلوبان.');
                $this->redirect('employee/edit/' . $empId);
            }

            if ($this->empModel->emailExists($data['email'], $empId)) {
                $this->setFlash('error', 'البريد الإلكتروني مسجل لموظف آخر.');
                $this->redirect('employee/edit/' . $empId);
            }

            if ($this->empModel->updateEmployee($empId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات الموظف بنجاح.');
                $this->redirect('employee/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('employee/edit/' . $empId);
            }
        } else {
            $data = [
                'title' => 'تعديل بيانات الموظف', 
                'employee' => $employee,
                'breadcrumb' => [
                    ['label' => 'الموظفين', 'url' => 'employee/index'],
                    ['label' => 'تعديل موظف', 'url' => '#']
                ]
            ];
            ob_start();
            $this->view('employee/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin'); 
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->empModel->deleteEmployee((int)$id)) {
                    $this->setFlash('success', 'تم حذف الموظف بنجاح.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف الموظف لوجود رواتب أو عهد مرتبطة به.');
            }
        }
        $this->redirect('employee/index');
    }
}