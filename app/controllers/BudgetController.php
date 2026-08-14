<?php
// app/controllers/BudgetController.php

class BudgetController extends Controller {
    
    private $budgetModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'accountant'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->budgetModel = $this->model('Budget');
    }

    public function index() {
        $year = $_GET['year'] ?? date('Y');
        $budgets = [];
        try {
            $budgets = $this->budgetModel->getAllBudgets($year);
        } catch (Throwable $e) {}

        $data = [
            'title' => 'الموازنة التقديرية (Budget)',
            'budgets' => is_array($budgets) ? $budgets : [],
            'selected_year' => $year,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'الموازنات', 'url' => 'budget/index']]
        ];
        
        ob_start(); $this->view('budget/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'fiscal_year' => trim($_POST['fiscal_year'] ?? date('Y')),
                'category_id' => (int)($_POST['category_id'] ?? 0),
                'amount'      => (float)($_POST['amount'] ?? 0),
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            try {
                if ($this->budgetModel->createBudget($data)) {
                    $this->setFlash('success', 'تم اعتماد وتخصيص الموازنة بنجاح.');
                    $this->redirect('budget/index?year=' . $data['fiscal_year']);
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', $e->getMessage());
            }
        }

        $db = Database::getInstance();
        $categories = [];
        try {
            $db->query("SELECT id, name FROM expense_categories WHERE company_id = :cid OR company_id IS NULL");
            $db->bind(':cid', Session::get('company_id') ?: 1);
            $res = $db->resultSet();
            $categories = is_array($res) ? $res : [];
        } catch (Throwable $e) {}

        $data = ['title' => 'تخصيص موازنة جديدة', 'categories' => $categories];
        ob_start(); $this->view('budget/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->budgetModel->deleteBudget((int)$id)) {
                $this->setFlash('success', 'تم حذف الموازنة بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
            }
        }
        $this->redirect('budget/index');
    }
}