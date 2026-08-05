<?php
// app/controllers/AdvanceController.php

class AdvanceController extends Controller {
    
    private Advance $advanceModel;

    public function __construct() {
        $this->requireAuth();
        $this->advanceModel = $this->model('Advance');
    }

    /**
     * عرض قائمة السلف
     */
    public function index(): void {
        $advances = $this->advanceModel->getAllAdvances();
        $isAdmin = Session::hasRole('admin');
        
        $data = [
            'title' => 'سجل السلف والعهد',
            'advances' => $advances,
            'is_admin' => $isAdmin,
            'flash' => $this->getFlash()
        ];
        
        $this->view('advances/index', $data);
    }

    /**
     * طلب سلفة جديدة
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id'    => (int)($_POST['employee_id'] ?? 0),
                'amount'         => (float)($_POST['amount'] ?? 0),
                'date'           => trim($_POST['date'] ?? date('Y-m-d')),
                'deduction_month'=> (int)($_POST['deduction_month'] ?? date('n')),
                'deduction_year' => (int)($_POST['deduction_year'] ?? date('Y')),
                'reason'         => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || $data['amount'] <= 0) {
                $this->setFlash('error', 'يرجى تحديد الموظف وإدخال مبلغ صحيح للسلفة.');
                $this->redirect('advance/create');
            }

            if ($this->advanceModel->createAdvance($data)) {
                $this->setFlash('success', 'تم تقديم طلب السلفة بنجاح وهو قيد المراجعة.');
                $this->redirect('advance/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الطلب.');
                $this->redirect('advance/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'طلب سلفة جديدة',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            
            $this->view('advances/create', $data);
        }
    }

    /**
     * الموافقة على السلفة
     */
    public function approve(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->updateStatus((int)$id, 'approved', Session::getUserId())) {
                $this->setFlash('success', 'تمت الموافقة على السلفة واعتمادها للخصم في الشهر المحدد.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة السلفة.');
            }
        }
        $this->redirect('advance/index');
    }

    /**
     * رفض السلفة
     */
    public function reject(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->updateStatus((int)$id, 'rejected', Session::getUserId())) {
                $this->setFlash('success', 'تم رفض طلب السلفة.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة السلفة.');
            }
        }
        $this->redirect('advance/index');
    }
}