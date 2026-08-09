<?php
// app/controllers/EmployeeContractController.php

class EmployeeContractController extends Controller {
    
    private $contractModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->contractModel = $this->model('EmployeeContract');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $contracts = $this->contractModel->getAllContracts();
        
        $data = [
            'title' => 'عقود الموظفين',
            'contracts' => $contracts,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'عقود الموظفين', 'url' => 'employeeContract/index']
            ]
        ];
        
        ob_start();
        $this->view('employeeContract/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $data = [
                'employee_id' => (int)$_POST['employee_id'],
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'basic_salary' => (float)($_POST['basic_salary'] ?? 0),
                'allowances' => (float)($_POST['allowances'] ?? 0),
                'status' => trim($_POST['status'] ?? 'active'),
                'notes' => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            if (empty($data['employee_id']) || empty($data['start_date']) || empty($data['basic_salary'])) {
                $this->setFlash('error', 'يرجى إكمال الحقول الأساسية (الموظف، تاريخ البداية، والراتب).');
            } else {
                if ($this->contractModel->createContract($data)) {
                    $this->setFlash('success', 'تم تسجيل عقد الموظف بنجاح.');
                    $this->redirect('employeeContract/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات العقد.');
                }
            }
        }

        $employees = $this->employeeModel->getAllEmployees();

        $data = [
            'title' => 'إضافة عقد موظف',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'عقود الموظفين', 'url' => 'employeeContract/index'],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('employeeContract/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    // 🟢 التعديل هنا: إزالة (string $id) وجعلها ($id = '') لتتوافق مع إصدارات PHP القديمة
    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->contractModel->deleteContract((int)$id)) {
                $this->setFlash('success', 'تم حذف عقد الموظف بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('employeeContract/index');
    }
}