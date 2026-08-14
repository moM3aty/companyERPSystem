<?php
// app/controllers/ExpenseController.php

class ExpenseController extends Controller {
    
    private $expenseModel;
    private $treasuryModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'accountant'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->expenseModel = $this->model('Expense');
        if (file_exists('../app/models/Treasury.php')) {
            $this->treasuryModel = $this->model('Treasury');
        }
    }

    public function index() {
        $expenses = [];
        try {
            $expenses = $this->expenseModel->getAllExpenses();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'المصروفات التشغيلية (Expenses)',
            'expenses' => is_array($expenses) ? $expenses : [],
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'المصروفات', 'url' => 'expense/index']]
        ];
        ob_start(); $this->view('expense/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $amount = (float)($_POST['amount'] ?? 0);
            $taxRate = (float)($_POST['tax_rate'] ?? 0);
            $taxAmount = $amount * ($taxRate / 100);
            $totalAmount = $amount + $taxAmount;

            $data = [
                'treasury_id'  => (int)($_POST['treasury_id'] ?? 0),
                'expense_date' => trim($_POST['expense_date'] ?? date('Y-m-d')),
                'category_id'  => (int)($_POST['category_id'] ?? 0), // استقبال ID التصنيف
                'amount'       => $amount,
                'tax_amount'   => $taxAmount,
                'total_amount' => $totalAmount,
                'cost_center'  => trim($_POST['cost_center'] ?? ''),
                'reference'    => trim($_POST['reference'] ?? ''),
                'notes'        => trim($_POST['notes'] ?? ''),
                'attachment'   => null
            ];

            if ($this->treasuryModel) {
                try {
                    $treasury = $this->treasuryModel->getTreasuryById($data['treasury_id']);
                    $bal = isset($treasury->current_balance) ? $treasury->current_balance : ($treasury->balance ?? 0);
                    if ($treasury && $bal < $totalAmount) {
                        $this->setFlash('error', 'الرصيد في هذا الصندوق غير كافٍ لسداد هذا المصروف.');
                        $this->redirect('expense/create');
                        return;
                    }
                } catch (Throwable $e) {}
            }

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/expenses/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['attachment'] = $fileName;
                }
            }

            try {
                $expId = $this->expenseModel->createExpense($data);
                if ($expId) {
                    $this->setFlash('success', 'تم حفظ المصروف وخصمه من الخزنة بنجاح.');
                    $this->redirect('expense/show/' . $expId);
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', 'حدث خطأ تقني: ' . $e->getMessage());
            }
        }

        $db = Database::getInstance();
        $cid = Session::get('company_id') ?: 1;
        $treasuries = [];
        $categories = []; // لجلب التصنيفات
        
        try {
            $db->query("SELECT id, name, current_balance, balance FROM treasuries WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $res = $db->resultSet();
            $treasuries = is_array($res) ? $res : [];
        } catch (Throwable $e) {}

        // جلب التصنيفات من الداتابيز
        try {
            $db->query("SELECT id, name FROM expense_categories WHERE company_id = :cid OR company_id IS NULL");
            $db->bind(':cid', $cid);
            $res = $db->resultSet();
            $categories = is_array($res) ? $res : [];
        } catch (Throwable $e) {}

        $data = [
            'title' => 'تسجيل مصروف جديد', 
            'treasuries' => $treasuries, 
            'categories' => $categories
        ];
        ob_start(); $this->view('expense/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('expense/index');
        
        $expense = null;
        try {
            $expense = $this->expenseModel->getExpenseById((int)$id);
        } catch (Throwable $e) {}
        
        if (!$expense) $this->redirect('expense/index');

        $data = ['title' => 'تفاصيل المصروف', 'expense' => $expense];
        ob_start(); $this->view('expense/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->expenseModel->deleteExpense((int)$id)) {
                $this->setFlash('success', 'تم مسح المصروف واسترجاع قيمته إلى الخزنة بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء المسح.');
            }
        }
        $this->redirect('expense/index');
    }
}