<?php
// app/controllers/ExpenseClaimController.php

class ExpenseClaimController extends Controller {
    
    private $claimModel;
    private $employeeModel;
    private $projectModel;

    public function __construct() {
        $this->requireAuth();
        $this->claimModel = $this->model('ExpenseClaim');
        if (file_exists('../app/models/Employee.php')) $this->employeeModel = $this->model('Employee');
        if (file_exists('../app/models/Project.php')) $this->projectModel = $this->model('Project');
    }

    public function index() {
        $claims = $this->claimModel->getAllClaims();
        $data = [
            'title' => 'مطالبات المصروفات (Expense Claims)',
            'claims' => $claims,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'مطالبات الموظفين', 'url' => 'expenseClaim/index']]
        ];
        ob_start(); $this->view('expenseClaim/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'claim_number'    => trim($_POST['claim_number'] ?? 'EXC-'.time()),
                'employee_id'     => (int)($_POST['employee_id'] ?? 0),
                'claim_date'      => trim($_POST['claim_date'] ?? date('Y-m-d')),
                'expense_type'    => trim($_POST['expense_type'] ?? ''),
                'amount'          => (float)($_POST['amount'] ?? 0),
                'vat_amount'      => (float)($_POST['vat_amount'] ?? 0),
                'currency'        => trim($_POST['currency'] ?? 'SAR'),
                'project_id'      => !empty($_POST['project_id']) ? (int)$_POST['project_id'] : null,
                'cost_center'     => trim($_POST['cost_center'] ?? ''),
                'business_purpose'=> trim($_POST['business_purpose'] ?? ''),
                'receipt_attachment' => null
            ];

            if (isset($_FILES['receipt_attachment']) && $_FILES['receipt_attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/expenses/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['receipt_attachment']['name']);
                if (move_uploaded_file($_FILES['receipt_attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['receipt_attachment'] = $fileName;
                }
            }

            if ($this->claimModel->createClaim($data)) {
                $this->setFlash('success', 'تم تقديم المطالبة وهي بانتظار الاعتماد.');
                $this->redirect('expenseClaim/index'); return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }

        $employees = $this->employeeModel ? $this->employeeModel->getAllEmployees() : [];
        $projects = $this->projectModel ? $this->projectModel->getAllProjects() : [];

        $data = [
            'title' => 'تقديم مطالبة مصروفات',
            'employees' => $employees,
            'projects' => $projects,
            'auto_num' => 'EXC-' . date('Ymd') . '-' . rand(10,99)
        ];
        ob_start(); $this->view('expenseClaim/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('expenseClaim/index');
        
        $claim = $this->claimModel->getClaimById((int)$id);
        if (!$claim) $this->redirect('expenseClaim/index');

        $db = Database::getInstance();
        $db->query("SELECT id, name, current_balance FROM treasuries WHERE company_id = :cid");
        $db->bind(':cid', Session::get('company_id') ?: 1);
        $treasuries = $db->resultSet();

        $data = ['title' => 'مطالبة #' . $claim->claim_number, 'claim' => $claim, 'treasuries' => $treasuries];
        ob_start(); $this->view('expenseClaim/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function approve($id = '') {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);
        if ($this->isPost() && !empty($id)) {
            $level = $_POST['level'] ?? '';
            $status = $_POST['status'] ?? '';
            $treasury = !empty($_POST['treasury_id']) ? (int)$_POST['treasury_id'] : null;

            if ($this->claimModel->approveClaim((int)$id, $level, $status, $treasury)) {
                $this->setFlash('success', 'تم تحديث حالة المطالبة وتسوية القيود إذا تم الصرف.');
            }
        }
        $this->redirect('expenseClaim/show/' . $id);
    }
}