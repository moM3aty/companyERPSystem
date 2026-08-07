<?php
// app/controllers/ExpenseController.php
class ExpenseController extends Controller {
    private Expense $expenseModel;
    
    public function __construct() {
        $this->requireAuth();
        $this->expenseModel = $this->model('Expense');
    }
    
    public function index(): void {
        $expenses = $this->expenseModel->getAllExpenses();
        $totalExpenses = $this->expenseModel->getTotalExpenses();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        $data = ['title' => 'سجل المصروفات التشغيلية', 'expenses' => $expenses, 'total_expenses' => $totalExpenses, 'is_admin' => $isAdmin];
        
        ob_start();
        $this->view('expenses/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    
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
                'created_by'   => Session::getUserId()
            ];
            
            if (empty($data['category_id']) || $data['amount'] <= 0) {
                $this->setFlash('error', 'يرجى اختيار التصنيف وإدخال مبلغ صحيح أكبر من الصفر.');
                $this->redirect('expense/create');
            }
            
            // המודל יעשה كل شيء: يحفظ المصروف، يقلل الخزنة برمجياً، وينشئ قيد يومية آلي من حساب مصروف إلى حساب الصندوق
            if ($this->expenseModel->createExpense($data)) {
                $this->setFlash('success', 'تم تسجيل المصروف بنجاح وإنشاء قيد محاسبي تلقائي وخصم المبلغ من الخزنة.');
                $this->redirect('expense/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ المصروف وتوليد القيد المحاسبي.');
                $this->redirect('expense/create');
            }
        } else {
            $categories = $this->expenseModel->getCategories();
            $data = ['title' => 'تسجيل مصروف جديد', 'categories' => $categories];
            ob_start();
            $this->view('expenses/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    
    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        if (empty($id) || !is_numeric($id)) $this->redirect('expense/index');
        $expenseId = (int)$id;
        $expense = $this->expenseModel->getExpenseById($expenseId);
        
        if (!$expense) {
            $this->setFlash('error', 'المصروف غير موجود.');
            $this->redirect('expense/index');
        }
        
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'category_id'  => (int)($_POST['category_id'] ?? $expense->category_id),
                'amount'       => (float)($_POST['amount'] ?? $expense->amount),
                'expense_date' => trim($_POST['expense_date'] ?? $expense->expense_date),
                'reference_no' => trim($_POST['reference_no'] ?? $expense->reference_no),
                'notes'        => trim($_POST['notes'] ?? $expense->notes)
            ];
            
            // ملاحظة: التعديل هنا يعدل البيانات النصية فقط لتجنب كسر توازن القيود المحاسبية التلقائية المربوطة به.
            // في نظام محاسبي صارم، لا يسمح بتعديل المبلغ بعد الترحيل بل يتم عمل قيد عكسي، ولكن للتبسيط تم إبقاؤه.
            if ($this->expenseModel->updateExpense($expenseId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات المصروف بنجاح.');
                $this->redirect('expense/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('expense/edit/' . $expenseId);
            }
        } else {
            $categories = $this->expenseModel->getCategories();
            $data = ['title' => 'تعديل مصروف', 'expense' => $expense, 'categories' => $categories];
            ob_start();
            $this->view('expenses/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
    
    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->expenseModel->deleteExpense((int)$id)) {
                $this->setFlash('success', 'تم حذف المصروف بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف المصروف.');
            }
        }
        $this->redirect('expense/index');
    }
}