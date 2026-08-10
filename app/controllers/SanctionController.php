<?php
// app/controllers/SanctionController.php

class SanctionController extends Controller {
    
    private $sanctionModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->sanctionModel =$this->model('Sanction');
        $this->employeeModel =$this->model('Employee');
    }

    public function index() {
        $sanctions = $this->sanctionModel->getAllSanctions();$data = [
            'title' => 'الجزاءات والمخالفات',
            'sanctions' => $sanctions,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الجزاءات', 'url' => 'sanction/index']
            ]
        ];
        
        ob_start();
        $this->view('sanction/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),                 'type'        => trim($_POST['type'] ?? 'warning'),
                'amount'      => (float)($_POST['amount'] ?? 0),                 'date'        => trim($_POST['date'] ?? date('Y-m-d')),
                'reason'      => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['reason'])) {$this->setFlash('error', 'يرجى تحديد الموظف وكتابة سبب المخالفة.');
            } else {
                if ($data['type'] === 'warning')$data['amount'] = 0; // التحذير ليس له خصم مالي
                
                if ($this->sanctionModel->createSanction($data)) {$this->setFlash('success', 'تم تسجيل القرار الإداري بنجاح وسيتم تطبيقه في الرواتب.');
                    $this->redirect('sanction/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ القرار.');
                }
            }
        }

        $employees =$this->employeeModel->getAllEmployees();

        $data = [
            'title' => 'توجيه إنذار أو خصم',
            'employees' => $employees,
            'breadcrumb' => [
                ['label' => 'الجزاءات', 'url' => 'sanction/index'],
                ['label' => 'إضافة قرار', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('sanction/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete($id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {$this->sanctionModel->deleteSanction((int)$id);$this->setFlash('success', 'تم سحب وإلغاء القرار الإداري.');
        }
        $this->redirect('sanction/index');
    }

    // 🟢 دالة استيراد الإكسيل الموحدة 🟢
    public function importExcel() {
        if ($this->isPost()) {$this->setFlash('success', 'تم استلام ملف الإكسيل. يتطلب تفعيل مكتبة المعالجة لقراءته آلياً.');
        }
        $this->redirect('sanction/index');
    }
}