<?php
// app/controllers/AccountController.php

class AccountController extends Controller {
    
    private $accountModel;

    public function __construct() {
        $this->requireAuth();
        $this->accountModel = $this->model('Account');
    }

    public function index() {
        $accounts = $this->accountModel->getAllAccounts();
        $data = [
            'title' => 'دليل الحسابات (Chart of Accounts)',
            'accounts' => $accounts,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'دليل الحسابات', 'url' => 'account/index']]
        ];
        ob_start(); $this->view('account/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'account_code' => trim($_POST['account_code'] ?? ''),
                'account_name' => trim($_POST['account_name'] ?? ''),
                'account_type' => trim($_POST['account_type'] ?? ''),
                'parent_id'    => trim($_POST['parent_id'] ?? ''),
                'description'  => trim($_POST['description'] ?? '')
            ];
            
            if ($this->accountModel->createAccount($data)) {
                $this->setFlash('success', 'تم إنشاء الحساب بنجاح.');
                $this->redirect('account/index'); return;
            }
        }
        $data = ['title' => 'إضافة حساب جديد', 'accounts' => $this->accountModel->getAllAccounts()];
        ob_start(); $this->view('account/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    // 🟢 إضافة دالة التعديل (Edit) 🟢
    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('account/index');
        
        $this->accountModel->db->query("SELECT * FROM accounting_accounts WHERE id = :id AND company_id = :cid");
        $this->accountModel->db->bind(':id', $id);
        $this->accountModel->db->bind(':cid', Session::get('company_id') ?: 1);
        $account = $this->accountModel->db->single();
        
        if (!$account) {
            $this->setFlash('error', 'الحساب غير موجود.');
            $this->redirect('account/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $parent_id = trim($_POST['parent_id'] ?? '');
            
            $sql = "UPDATE accounting_accounts SET 
                    account_code = :code, account_name = :name, account_type = :type, 
                    parent_id = :parent, description = :desc 
                    WHERE id = :id AND company_id = :cid";
            
            $this->accountModel->db->query($sql);
            $this->accountModel->db->bind(':code', trim($_POST['account_code'] ?? ''));
            $this->accountModel->db->bind(':name', trim($_POST['account_name'] ?? ''));
            $this->accountModel->db->bind(':type', trim($_POST['account_type'] ?? ''));
            $this->accountModel->db->bind(':parent', !empty($parent_id) ? $parent_id : null);
            $this->accountModel->db->bind(':desc', trim($_POST['description'] ?? ''));
            $this->accountModel->db->bind(':id', $id);
            $this->accountModel->db->bind(':cid', Session::get('company_id') ?: 1);
            
            if ($this->accountModel->db->execute()) {
                $this->setFlash('success', 'تم تعديل بيانات الحساب بنجاح.');
                $this->redirect('account/index'); return;
            }
        }

        $data = ['title' => 'تعديل حساب محاسبي', 'account' => $account, 'accounts' => $this->accountModel->getAllAccounts()];
        ob_start(); $this->view('account/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->accountModel->deleteAccount((int)$id);
            $this->setFlash('success', 'تم حذف الحساب.');
        }
        $this->redirect('account/index');
    }
}