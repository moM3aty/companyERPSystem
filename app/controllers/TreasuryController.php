<?php
// app/controllers/TreasuryController.php

class TreasuryController extends Controller {
    
    private $treasuryModel;
    private $accountModel;

    public function __construct() {
        $this->requireAuth();
        $this->treasuryModel = $this->model('Treasury');
        if (file_exists('../app/models/Account.php')) $this->accountModel = $this->model('Account');
    }

    public function index() {
        $treasuries = $this->treasuryModel->getAllTreasuries();
        $data = [
            'title' => 'إدارة الصناديق والبنوك',
            'treasuries' => $treasuries,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'الصناديق والبنوك', 'url' => 'treasury/index']]
        ];
        ob_start(); $this->view('treasury/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? 'Cash'),
                'account_number' => trim($_POST['account_number'] ?? ''),
                'currency' => trim($_POST['currency'] ?? 'SAR'),
                'opening_balance' => (float)($_POST['opening_balance'] ?? 0),
                'chart_account_id' => !empty($_POST['chart_account_id']) ? (int)$_POST['chart_account_id'] : null
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'الرجاء إدخال اسم الخزنة/البنك.');
            } else {
                if ($this->treasuryModel->createTreasury($data)) {
                    $this->setFlash('success', 'تم إنشاء الصندوق المحاسبي بنجاح.');
                    $this->redirect('treasury/index'); return;
                }
            }
        }
        $accounts = $this->accountModel ? $this->accountModel->getAllAccounts() : [];
        $data = ['title' => 'إضافة خزنة / بنك', 'accounts' => $accounts];
        ob_start(); $this->view('treasury/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('treasury/index');
        $treasury = $this->treasuryModel->getTreasuryById((int)$id);
        if (!$treasury) $this->redirect('treasury/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? 'Cash'),
                'account_number' => trim($_POST['account_number'] ?? ''),
                'currency' => trim($_POST['currency'] ?? 'SAR'),
                'chart_account_id' => !empty($_POST['chart_account_id']) ? (int)$_POST['chart_account_id'] : null
            ];

            if ($this->treasuryModel->updateTreasury((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات الصندوق.');
                $this->redirect('treasury/index'); return;
            }
        }
        $accounts = $this->accountModel ? $this->accountModel->getAllAccounts() : [];
        $data = ['title' => 'تعديل الخزنة/البنك', 'treasury' => $treasury, 'accounts' => $accounts];
        ob_start(); $this->view('treasury/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}