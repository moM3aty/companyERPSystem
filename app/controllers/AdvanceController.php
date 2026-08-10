<?php
// app/controllers/AdvanceController.php

class AdvanceController extends Controller {
    
    private $advanceModel;

    public function __construct() {
        $this->requireAuth();
        $this->advanceModel =$this->model('Advance');
    }

    public function index() {
        $advances = $this->advanceModel->getAllAdvances();$data = [
            'title' => 'السلف والعهد النقدية',
            'advances' => $advances,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'السلف', 'url' => 'advance/index']
            ]
        ];
        
        ob_start();
        $this->view('advance/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id'     => (int)($_POST['employee_id'] ?? 0),
                'amount'          => (float)($_POST['amount'] ?? 0),                 'date'            => trim($_POST['date'] ?? date('Y-m-d')),
                'deduction_month' => (int)($_POST['deduction_month'] ?? date('n')),
                'deduction_year'  => (int)($_POST['deduction_year'] ?? date('Y')),
                'reason'          => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || $data['amount'] <= 0) {$this->setFlash('error', 'الرجاء اختيار الموظف وإدخال مبلغ السلفة بشكل صحيح.');
            } else {
                if ($this->advanceModel->createAdvance($data)) {$this->setFlash('success', 'تم تقديم طلب السلفة بنجاح وهو بانتظار الاعتماد.');
                    $this->redirect('advance/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تقديم الطلب.');
                }
            }
        }

        $employeeModel = $this->model('Employee');$employees = [];
        if (method_exists($employeeModel, 'getAllEmployees')) {
            $employees =$employeeModel->getAllEmployees();
        }

        $data = [
            'title' => 'طلب سلفة جديدة',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'السلف', 'url' => 'advance/index'],
                ['label' => 'طلب جديد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('advance/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function updateStatus() {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);
        
        if ($this->isPost()) {
            $advanceId = (int)($_POST['advance_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            
            if ($advanceId > 0 && in_array($status, ['approved', 'rejected'])) {
                if ($this->advanceModel->updateStatus($advanceId, $status, Session::getUserId())) {$this->setFlash('success', 'تم تحديث حالة طلب السلفة بنجاح.');
                } else {
                    $this->setFlash('error', 'فشل في تحديث حالة الطلب.');
                }
            }
        }
        $this->redirect('advance/index');
    }

    public function delete($id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->deleteAdvance((int)$id)) {$this->setFlash('success', 'تم إلغاء وحذف طلب السلفة.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('advance/index');
    }
}