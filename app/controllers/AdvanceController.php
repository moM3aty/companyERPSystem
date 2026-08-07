<?php
// app/controllers/AdvanceController.php
class AdvanceController extends Controller {
    private Advance $advanceModel;
    public function __construct() {
        $this->requireAuth();
        $this->advanceModel = $this->model('Advance');
    }
    public function index(): void {
        $advances = $this->advanceModel->getAllAdvances();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        $data = ['title' => 'سجل السلف والعهد', 'advances' => $advances, 'is_admin' => $isAdmin];
        ob_start();
        $this->view('advances/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
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
            $data = ['title' => 'طلب سلفة جديدة', 'employees' => $employees];
            ob_start();
            $this->view('advances/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        if (empty($id) || !is_numeric($id)) $this->redirect('advance/index');
        $advanceId = (int)$id;
        $advance = $this->advanceModel->getAdvanceById($advanceId);
        if (!$advance || $advance->status !== 'pending') {
            $this->setFlash('error', 'لا يمكن تعديل السلفة لأنها معتمدة أو ملغية.');
            $this->redirect('advance/index');
        }
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_id'    => (int)($_POST['employee_id'] ?? $advance->employee_id),
                'amount'         => (float)($_POST['amount'] ?? $advance->amount),
                'date'           => trim($_POST['date'] ?? $advance->date),
                'deduction_month'=> (int)($_POST['deduction_month'] ?? $advance->deduction_month),
                'deduction_year' => (int)($_POST['deduction_year'] ?? $advance->deduction_year),
                'reason'         => trim($_POST['reason'] ?? $advance->reason)
            ];
            if ($this->advanceModel->updateAdvance($advanceId, $data)) {
                $this->setFlash('success', 'تم تعديل السلفة بنجاح.');
                $this->redirect('advance/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('advance/edit/' . $advanceId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            $data = ['title' => 'تعديل سلفة', 'advance' => $advance, 'employees' => $employees];
            ob_start();
            $this->view('advances/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    public function approve(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->updateStatus((int)$id, 'approved', Session::getUserId())) {
                $this->setFlash('success', 'تمت الموافقة على السلفة واعتمادها للخصم في الشهر المحدد.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة السلفة.');
            }
        }
        $this->redirect('advance/index');
    }
    public function reject(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->updateStatus((int)$id, 'rejected', Session::getUserId())) {
                $this->setFlash('success', 'تم رفض طلب السلفة.');
            } else {
                $this->setFlash('error', 'فشل في تحديث حالة السلفة.');
            }
        }
        $this->redirect('advance/index');
    }
    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->advanceModel->deleteAdvance((int)$id)) {
                $this->setFlash('success', 'تم حذف طلب السلفة.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('advance/index');
    }
}