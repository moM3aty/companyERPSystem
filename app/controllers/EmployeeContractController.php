<?php
// المسار: app/controllers/EmployeeContractController.php

class EmployeeContractController extends Controller {
    
    private EmployeeContract $contractModel;

    public function __construct() {
        $this->requireAuth();
        $this->contractModel = $this->model('EmployeeContract');
    }

    public function index(): void {
        $contracts = $this->contractModel->getAllContracts();
        
        $data = [
            'title' => 'عقود الموظفين',
            'contracts' => $contracts,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'العقود', 'url' => 'employeeContract/index']
            ]
        ];
        
        ob_start();
        $this->view('employee_contracts/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $contractNumber = trim($_POST['contract_number'] ?? '');
            if (empty($contractNumber)) {
                $contractNumber = 'EMP-CON-' . date('Ymd') . '-' . random_int(100, 999);
            }

            $data = [
                'contract_number' => $contractNumber,
                'title'           => trim($_POST['title'] ?? 'عقد عمل محدد المدة'),
                'employee_id'     => (int)($_POST['employee_id'] ?? 0),
                'start_date'      => trim($_POST['start_date'] ?? ''),
                'end_date'        => trim($_POST['end_date'] ?? ''),
                'value'           => (float)($_POST['value'] ?? 0.00),
                'status'          => trim($_POST['status'] ?? 'active')
            ];

            if (empty($data['employee_id']) || empty($data['start_date']) || empty($data['end_date'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإجبارية.');
                $this->redirect('employeeContract/create');
            }

            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.');
                $this->redirect('employeeContract/create');
            }

            if ($this->contractModel->createContract($data)) {
                $this->setFlash('success', 'تم توثيق العقد الوظيفي بنجاح.');
                $this->redirect('employeeContract/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ العقد.');
                $this->redirect('employeeContract/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, position, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'إبرام عقد جديد',
                'employees' => $employees,
                'breadcrumb' => [
                    ['label' => 'عقود الموظفين', 'url' => 'employeeContract/index'],
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('employee_contracts/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function terminate(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->contractModel->updateStatus((int)$id, 'terminated')) {
                $this->setFlash('success', 'تم إنهاء العقد بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإنهاء.');
            }
        }
        $this->redirect('employeeContract/index');
    }
}