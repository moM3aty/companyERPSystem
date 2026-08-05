<?php
// app/controllers/ExpenseController.php

class ExpenseController extends Controller {
    
    /** @var Expense */
    private Expense $expenseModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->expenseModel = $this->model('Expense');
    }

    /**
     * عرض قائمة المصروفات
     */
    public function index(): void {
        $expenses = $this->expenseModel->getAllExpenses();
        $totalExpenses = $this->expenseModel->getTotalExpenses();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'سجل المصروفات',
            'expenses' => $expenses,
            'total_expenses' => $totalExpenses,
            'is_admin' => $isAdmin,
            'flash' => $this->getFlash()
        ];
        
        $this->view('expenses/index', $data);
    }

    /**
     * تسجيل مصروف جديد
     */
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'category_id'  => (int)($_POST['category_id'] ?? 0),
                'amount'       => (float)($_POST['amount'] ?? 0.0),
                'expense_date' => trim($_POST['expense_date'] ?? date('Y-m-d')),
                'reference_no' => trim($_POST['reference_no'] ?? ''),
                'notes'        => trim($_POST['notes'] ?? ''),
                'recorded_by'  => Session::getUserId()
            ];

            if (empty($data['category_id']) || $data['amount'] <= 0) {
                $this->setFlash('error', 'يرجى اختيار التصنيف وإدخال مبلغ صحيح أكبر من الصفر.');
                $this->redirect('expense/create');
            }

            if ($this->expenseModel->createExpense($data)) {
                $this->setFlash('success', 'تم تسجيل المصروف بنجاح.');
                $this->redirect('expense/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ المصروف.');
                $this->redirect('expense/create');
            }
        } else {
            $categories = $this->expenseModel->getCategories();
            
            $data = [
                'title' => 'تسجيل مصروف جديد',
                'categories' => $categories,
                'flash' => $this->getFlash()
            ];
            
            $this->view('expenses/create', $data);
        }
    }

    /**
     * حذف مصروف
     */
    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->expenseModel->delete((int)$id)) {
                $this->setFlash('success', 'تم حذف المصروف بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف المصروف.');
            }
        }
        $this->redirect('expense/index');
    }
}