<?php
// app/controllers/SanctionController.php

class SanctionController extends Controller {
    
    /** @var Sanction */
    private Sanction $sanctionModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->sanctionModel = $this->model('Sanction');
    }

    /**
     * عرض الصفحة الرئيسية لسجل الجزاءات
     */
    public function index(): void {
        $sanctions = $this->sanctionModel->getAllSanctions();
        $totalDeductions = $this->sanctionModel->getTotalDeductions();
        $warningsCount = $this->sanctionModel->getWarningsCount();
        
        $data = [
            'title' => 'الجزاءات والمخالفات',
            'sanctions' => $sanctions,
            'total_deductions' => $totalDeductions,
            'warnings_count' => $warningsCount,
            'flash' => $this->getFlash()
        ];
        
        $this->view('sanctions/index', $data);
    }

    /**
     * إضافة جزاء جديد
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $type = $_POST['type'] ?? 'warning';
            // إذا كان إنذاراً، فإن المبلغ يكون صفراً تلقائياً
            $amount = $type === 'deduction' ? (float)($_POST['amount'] ?? 0) : 0.00;
            
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'type'        => $type,
                'amount'      => $amount,
                'date'        => trim($_POST['date'] ?? date('Y-m-d')),
                'reason'      => trim($_POST['reason'] ?? ''),
                'created_by'  => Session::getUserId() // سحب معرف المدير الحالي من الجلسة
            ];

            if (empty($data['employee_id']) || empty($data['reason'])) {
                $this->setFlash('error', 'يرجى تعبئة كافة الحقول المطلوبة.');
                $this->redirect('sanction/create');
            }

            if ($this->sanctionModel->createSanction($data)) {
                $this->setFlash('success', 'تم توقيع الجزاء وتوثيقه في السجل بنجاح.');
                $this->redirect('sanction/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الجزاء، يرجى المحاولة لاحقاً.');
                $this->redirect('sanction/create');
            }
            
        } else {
            // جلب الموظفين لعرضهم في قائمة الاختيار
            $db = Database::getInstance();
            $db->query("SELECT id, name, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'توقيع جزاء جديد',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            
            $this->view('sanctions/create', $data);
        }
    }

    /**
     * حذف وإلغاء جزاء
     */
    public function delete(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $sanctionId = (int)$id;
            
            if ($this->sanctionModel->delete($sanctionId)) {
                $this->setFlash('success', 'تم إلغاء الجزاء وحذفه من السجل بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ، لم يتم حذف الجزاء.');
            }
        }
        $this->redirect('sanction/index');
    }
}