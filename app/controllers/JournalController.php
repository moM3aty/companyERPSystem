<?php
// app/controllers/JournalController.php

class JournalController extends Controller {
    
    private $journalModel;
    private $accountModel;

    public function __construct() {
        $this->requireAuth();
        $this->journalModel = $this->model('JournalEntry');
        $this->accountModel = $this->model('Account');
    }

    public function index() {
        $journals = $this->journalModel->getAllJournals();
        $data = [
            'title' => 'القيود اليومية (Journal Entries)',
            'journals' => $journals,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'القيود', 'url' => 'journal/index']]
        ];
        ob_start(); $this->view('journal/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $accounts = $_POST['account_id'] ?? [];
            $debits = $_POST['debit'] ?? [];
            $credits = $_POST['credit'] ?? [];
            $descs = $_POST['line_desc'] ?? [];
            $ccs = $_POST['cost_center'] ?? [];

            $totalDebit = 0; $totalCredit = 0;
            $items = [];

            for ($i = 0; $i < count($accounts); $i++) {
                if (!empty($accounts[$i])) {
                    $d = (float)($debits[$i] ?? 0);
                    $c = (float)($credits[$i] ?? 0);
                    $totalDebit += $d; $totalCredit += $c;
                    $items[] = [
                        'account_id' => $accounts[$i], 'description' => $descs[$i],
                        'debit' => $d, 'credit' => $c, 'cost_center' => $ccs[$i]
                    ];
                }
            }

            if ($totalDebit !== $totalCredit || $totalDebit == 0) {
                $this->setFlash('error', 'القيود غير متزنة! يجب أن يتساوى إجمالي المدين مع الدائن.');
            } else {
                $data = [
                    'journal_number' => 'JV-' . time(),
                    'date'           => $_POST['date'],
                    'description'    => $_POST['description'],
                    'total_amount'   => $totalDebit
                ];
                if ($this->journalModel->createEntry($data, $items)) {
                    $this->setFlash('success', 'تم تسجيل واعتماد القيد بنجاح (Double-Entry).');
                    $this->redirect('journal/index'); return;
                }
            }
        }

        $data = ['title' => 'إنشاء قيد محاسبي مزدوج', 'accounts' => $this->accountModel->getAllAccounts()];
        ob_start(); $this->view('journal/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}