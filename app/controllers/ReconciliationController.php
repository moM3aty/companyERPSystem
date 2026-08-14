<?php
// app/controllers/ReconciliationController.php

class ReconciliationController extends Controller {
    
    private $recModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireAnyRole(['admin', 'super_admin', 'manager', 'accountant']);
        $this->recModel = $this->model('BankReconciliation');
    }

    public function index() {
        $recs = $this->recModel->getAllReconciliations();
        $data = [
            'title' => 'تسويات البنوك (Bank Reconciliation)',
            'reconciliations' => $recs,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'تسوية البنوك', 'url' => 'reconciliation/index']]
        ];
        ob_start(); $this->view('reconciliation/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        $db = Database::getInstance();
        $banks = [];
        
        try {
            $db->query("SELECT id, name, type, current_balance FROM treasuries WHERE company_id = :cid AND type = 'Bank'");
            $db->bind(':cid', Session::get('company_id') ?: 1);
            $banks = $db->resultSet();
        } catch (Exception $e) {}

        $transactions = [];
        $selectedBank = null;
        $statementDate = $_GET['date'] ?? date('Y-m-d');
        $bankId = $_GET['bank_id'] ?? '';

        if (!empty($bankId) && is_numeric($bankId)) {
            try {
                $db->query("SELECT * FROM treasuries WHERE id = :id LIMIT 1");
                $db->bind(':id', $bankId);
                $selectedBank = $db->single();
                
                if ($selectedBank) {
                    $transactions = $this->recModel->getUnclearedTransactions($selectedBank->id, $statementDate);
                }
            } catch (Exception $e) {}
        }

        if ($this->isPost()) {
            $data = [
                'treasury_id' => (int)$_POST['bank_id'],
                'statement_date' => $_POST['statement_date'],
                'system_balance' => (float)$_POST['system_balance'],
                'statement_balance' => (float)$_POST['statement_balance'],
                'difference' => (float)$_POST['difference'],
                'notes' => trim($_POST['notes'] ?? '')
            ];

            $clearedSources = $_POST['cleared_source'] ?? [];
            $clearedIds = $_POST['cleared_id'] ?? [];
            $clearedAmounts = $_POST['cleared_amount'] ?? [];
            $clearedTypes = $_POST['cleared_type'] ?? [];

            $clearedItems = [];
            for ($i = 0; $i < count($clearedIds); $i++) {
                $clearedItems[] = [
                    'source' => $clearedSources[$i],
                    'id' => $clearedIds[$i],
                    'amount' => $clearedAmounts[$i],
                    'type' => $clearedTypes[$i]
                ];
            }

            if (abs($data['difference']) > 0.01) {
                $this->setFlash('error', 'لا يمكن حفظ التسوية! يوجد فرق غير مبرر (Difference != 0).');
            } else {
                $recId = $this->recModel->saveReconciliation($data, $clearedItems);
                if ($recId) {
                    $this->setFlash('success', 'تمت مطابقة رصيد البنك مع النظام بنجاح!');
                    $this->redirect('reconciliation/show/' . $recId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ التسوية.');
                }
            }
        }

        $data = [
            'title' => 'إجراء تسوية بنكية جديدة',
            'banks' => $banks,
            'selected_bank' => $selectedBank,
            'statement_date' => $statementDate,
            'transactions' => $transactions
        ];
        ob_start(); $this->view('reconciliation/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('reconciliation/index');
        
        $rec = $this->recModel->getReconciliationById((int)$id);
        if (!$rec) $this->redirect('reconciliation/index');

        $data = ['title' => 'تقرير التسوية البنكية', 'reconciliation' => $rec];
        ob_start(); $this->view('reconciliation/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}