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
            'title' => 'عقود الموظفين (Employment Contracts)',
            'contracts' => $contracts,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'العقود', 'url' => 'employeeContract/index']
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
                'employee_id'  => (int)($_POST['employee_id'] ?? 0),
                'start_date'   => trim($_POST['start_date'] ?? ''),
                'end_date'     => trim($_POST['end_date'] ?? ''),
                'basic_salary' => (float)($_POST['basic_salary'] ?? 0),
                'allowances'   => (float)($_POST['allowances'] ?? 0),
                'status'       => trim($_POST['status'] ?? 'active'),
                'notes'        => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['start_date'])) {
                $this->setFlash('error', 'يرجى تحديد الموظف وتاريخ بداية العقد.');
            } else {
                if ($this->contractModel->createContract($data)) {
                    $this->setFlash('success', 'تم تسجيل عقد الموظف وتحديث راتبه الأساسي بنجاح.');
                    $this->redirect('employeeContract/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ العقد.');
                }
            }
        }

        $employees = [];
        if (method_exists($this->employeeModel, 'getAllEmployees')) {
            $employees = $this->employeeModel->getAllEmployees();
        }

        $data = [
            'title' => 'إضافة عقد وظيفي',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'العقود', 'url' => 'employeeContract/index'],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('employeeContract/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('employeeContract/index');
        
        $contract = $this->contractModel->getContractById((int)$id);
        if (!$contract) {
            $this->setFlash('error', 'العقد غير موجود.');
            $this->redirect('employeeContract/index');
        }

        if ($this->isPost()) {
            $data = [
                'employee_id'  => (int)($_POST['employee_id'] ?? 0),
                'start_date'   => trim($_POST['start_date'] ?? ''),
                'end_date'     => trim($_POST['end_date'] ?? ''),
                'basic_salary' => (float)($_POST['basic_salary'] ?? 0),
                'allowances'   => (float)($_POST['allowances'] ?? 0),
                'status'       => trim($_POST['status'] ?? 'active'),
                'notes'        => trim($_POST['notes'] ?? '')
            ];

            if ($this->contractModel->updateContract((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات العقد بنجاح.');
                $this->redirect('employeeContract/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
            }
        }

        $employees = [];
        if (method_exists($this->employeeModel, 'getAllEmployees')) {
            $employees = $this->employeeModel->getAllEmployees();
        }

        $data = [
            'title' => 'تعديل عقد وظيفي',
            'contract' => $contract,
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'العقود', 'url' => 'employeeContract/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('employeeContract/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->contractModel->deleteContract((int)$id);
            $this->setFlash('success', 'تم حذف العقد بنجاح.');
        }
        $this->redirect('employeeContract/index');
    }

    public function importExcel() {
        if ($this->isPost()) {
            $this->setFlash('success', 'Excel file received successfully. (Requires PhpSpreadsheet library setup for processing).');
        }
        $this->redirect('employeeContract/index');
    }
} 