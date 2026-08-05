<?php
class AccountController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
        // يتطلب صلاحية admin أو محاسب
        if (!in_array($_SESSION['user_role'], ['admin', 'editor'])) {
            $this->setFlash('error', 'ليس لديك صلاحية للوصول إلى المحاسبة');
            $this->redirect('dashboard');
        }
    }

    // عرض دفتر الأستاذ (سجل القيود)
    public function ledger() {
        $db = Database::getInstance();
        $db->query('
            SELECT je.*, u.name as created_by_name
            FROM journal_entries je
            LEFT JOIN users u ON je.created_by = u.id
            ORDER BY je.id DESC
        ');
        $entries = $db->resultSet();
        
        $data = [
            'title' => 'دفتر الأستاذ',
            'entries' => $entries,
            'flash' => $this->getFlash()
        ];
        $this->view('account/ledger', $data);
    }

    // عرض الميزانية العمومية
    public function balanceSheet() {
        // حساب الأصول والخصوم وحقوق الملكية
        $accountingModel = $this->model('Accounting');
        
        // جلب الحسابات من شجرة الحسابات
        $db = Database::getInstance();
        $db->query('SELECT * FROM chart_of_accounts WHERE is_active = 1 ORDER BY code');
        $accounts = $db->resultSet();
        
        $balances = [];
        foreach ($accounts as $acc) {
            $balances[$acc->id] = $accountingModel->getAccountBalance($acc->id);
        }
        
        $data = [
            'title' => 'الميزانية العمومية',
            'accounts' => $accounts,
            'balances' => $balances,
            'flash' => $this->getFlash()
        ];
        $this->view('account/balance_sheet', $data);
    }

    // عرض قائمة الدخل
    public function incomeStatement() {
        // حساب الإيرادات والمصروفات
        $accountingModel = $this->model('Accounting');
        
        $db = Database::getInstance();
        $db->query('SELECT * FROM chart_of_accounts WHERE type IN ("revenue", "expense") ORDER BY code');
        $accounts = $db->resultSet();
        
        $balances = [];
        foreach ($accounts as $acc) {
            $balances[$acc->id] = $accountingModel->getAccountBalance($acc->id);
        }
        
        $data = [
            'title' => 'قائمة الدخل',
            'accounts' => $accounts,
            'balances' => $balances,
            'flash' => $this->getFlash()
        ];
        $this->view('account/income_statement', $data);
    }

    // إنشاء قيد يومي يدوي (للمحاسب)
    public function createJournal() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $date = $_POST['entry_date'];
            $description = trim($_POST['description']);
            $lines = $_POST['lines'] ?? []; // مصفوفة من الحسابات والمبالغ
            
            // تحويل المدخلات إلى صيغة مناسبة
            $journalLines = [];
            foreach ($lines as $line) {
                if (!empty($line['account_id']) && ($line['debit'] > 0 || $line['credit'] > 0)) {
                    $journalLines[] = [
                        'account_id' => $line['account_id'],
                        'debit' => (float) $line['debit'],
                        'credit' => (float) $line['credit'],
                        'description' => $line['description'] ?? null,
                    ];
                }
            }
            
            if (empty($journalLines)) {
                $this->setFlash('error', 'يجب إدخال سطر واحد على الأقل');
                $this->redirect('account/create-journal');
            }
            
            // التحقق من تساوي المدين والدائن
            $totalDebit = array_sum(array_column($journalLines, 'debit'));
            $totalCredit = array_sum(array_column($journalLines, 'credit'));
            if (abs($totalDebit - $totalCredit) > 0.01) {
                $this->setFlash('error', 'مجموع المدين يجب أن يساوي مجموع الدائن');
                $this->redirect('account/create-journal');
            }
            
            $accountingModel = $this->model('Accounting');
            try {
                $entryId = $accountingModel->createJournalEntry(
                    $date,
                    $description,
                    'manual',
                    null,
                    $_SESSION['user_id'],
                    $journalLines
                );
                $this->setFlash('success', 'تم إنشاء القيد رقم ' . $entryId . ' بنجاح');
            } catch (Exception $e) {
                $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
            }
            
            $this->redirect('account/ledger');
        } else {
            // جلب شجرة الحسابات للاختيار
            $db = Database::getInstance();
            $db->query('SELECT * FROM chart_of_accounts WHERE is_active = 1 ORDER BY code');
            $accounts = $db->resultSet();
            
            $data = [
                'title' => 'إنشاء قيد يومي',
                'accounts' => $accounts,
                'flash' => $this->getFlash()
            ];
            $this->view('account/create_journal', $data);
        }
    }
}