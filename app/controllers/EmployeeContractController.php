<?php
// app/controllers/EmployeeContractController.php

class EmployeeContractController extends Controller {
    
    /** @var EmployeeContract */
    private EmployeeContract $contractModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->contractModel = $this->model('EmployeeContract');
    }

    /**
     * عرض قائمة عقود الموظفين
     */
    public function index(): void {
        $contracts = $this->contractModel->getAllContracts();
        
        $data = [
            'title' => 'عقود الموظفين',
            'contracts' => $contracts,
            'flash' => $this->getFlash()
        ];
        
        $this->view('employee_contracts/index', $data);
    }

    /**
     * إنشاء عقد وظيفي جديد
     */
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // توليد رقم عقد تلقائي إذا لم يتم إدخاله
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
                $this->setFlash('error', 'يرجى تعبئة الحقول الإجبارية (الموظف، تاريخ البداية والنهاية).');
                $this->redirect('employeeContract/create');
            }

            if (strtotime($data['end_date']) <= strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ نهاية العقد يجب أن يكون بعد تاريخ بدايته.');
                $this->redirect('employeeContract/create');
            }

            if ($this->contractModel->createContract($data)) {
                $this->setFlash('success', 'تم تسجيل وتوثيق العقد الوظيفي بنجاح.');
                $this->redirect('employeeContract/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ العقد، قد يكون رقم العقد مكرراً.');
                $this->redirect('employeeContract/create');
            }
        } else {
            // جلب الموظفين لعرضهم في القائمة
            $db = Database::getInstance();
            $db->query("SELECT id, name, position, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'إبرام عقد جديد',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            
            $this->view('employee_contracts/create', $data);
        }
    }

    /**
     * إنهاء عقد موظف
     */
    public function terminate(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->contractModel->updateStatus((int)$id, 'terminated')) {
                $this->setFlash('success', 'تم إنهاء العقد بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة إنهاء العقد.');
            }
        }
        $this->redirect('employeeContract/index');
    }
}