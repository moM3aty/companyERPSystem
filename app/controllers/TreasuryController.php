<?php
// app/controllers/TreasuryController.php

class TreasuryController extends Controller {
    
    private Treasury $treasuryModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        $this->treasuryModel = $this->model('Treasury');
    }

    public function index(): void {
        $treasuries = $this->treasuryModel->getAllTreasuries();
        
        $totalCash = 0;
        $totalBank = 0;
        foreach ($treasuries as $t) {
            if ($t->type === 'cash') $totalCash += $t->current_balance;
            if ($t->type === 'bank') $totalBank += $t->current_balance;
        }

        $data = [
            'title' => 'الصندوق والبنوك',
            'treasuries' => $treasuries,
            'total_cash' => $totalCash,
            'total_bank' => $totalBank,
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => '#'],
                ['label' => 'الصندوق والبنوك', 'url' => 'treasury/index']
            ]
        ];

        ob_start();
        $this->view('treasury/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function transactions(): void {
        $transactions = $this->treasuryModel->getAllTransactions();
        
        $data = [
            'title' => 'السجل المالي للصندوق',
            'transactions' => $transactions,
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => '#'],
                ['label' => 'الحركات المالية', 'url' => 'treasury/transactions']
            ]
        ];

        ob_start();
        $this->view('treasury/transactions', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function createTransaction(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'treasury_id'      => (int)($_POST['treasury_id'] ?? 0),
                'transaction_type' => trim($_POST['transaction_type'] ?? ''),
                'amount'           => (float)($_POST['amount'] ?? 0.0),
                'transaction_date' => trim($_POST['transaction_date'] ?? date('Y-m-d')),
                'reference'        => trim($_POST['reference'] ?? ''),
                'description'      => trim($_POST['description'] ?? ''),
                'created_by'       => Session::getUserId(),
                'account_id'       => !empty($_POST['account_id']) ? (int)$_POST['account_id'] : null
            ];

            if (empty($data['treasury_id']) || empty($data['transaction_type']) || $data['amount'] <= 0 || empty($data['description'])) {
                $this->setFlash('error', 'يرجى تعبئة كافة الحقول المطلوبة بمبالغ صحيحة.');
                $this->redirect('treasury/createTransaction');
            }

            if ($this->treasuryModel->createTransaction($data, true)) { // true = Generate Auto Journal
                $this->setFlash('success', 'تم تسجيل الحركة المالية بنجاح وتحديث الأرصدة والقيود المحاسبية.');
                $this->redirect('treasury/transactions');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء معالجة الحركة المالية. يرجى مراجعة المدخلات.');
                $this->redirect('treasury/createTransaction');
            }
        } else {
            $treasuries = $this->treasuryModel->getAllTreasuries();
            $accountModel = $this->model('Account');
            $accounts = $accountModel->getChartOfAccounts();
            
            $data = [
                'title' => 'تسجيل سند حركة مالية مباشر',
                'treasuries' => $treasuries,
                'accounts' => $accounts,
                'breadcrumb' => [
                    ['label' => 'الصندوق والبنوك', 'url' => 'treasury/index'],
                    ['label' => 'سند جديد', 'url' => '#']
                ]
            ];

            ob_start();
            $this->view('treasury/create_transaction', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}