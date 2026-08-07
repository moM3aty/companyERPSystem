<?php
// app/controllers/AccountController.php

class AccountController extends Controller {
    
    private Account $accountModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'editor', 'manager']);
        $this->accountModel = $this->model('Account');
    }

    public function index(): void {
        $this->tree();
    }

    public function tree(): void {
        $accounts = $this->accountModel->getChartOfAccounts();
        $data = [
            'title' => 'دليل الحسابات المالي', 
            'accounts' => $accounts,
            'breadcrumb' => [
                ['label' => 'الحسابات والمالية', 'url' => '#'],
                ['label' => 'شجرة الحسابات', 'url' => 'account/tree']
            ]
        ];
        
        ob_start();
        $this->view('account/tree', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'code'      => trim($_POST['code'] ?? ''),
                'name'      => trim($_POST['name'] ?? ''),
                'type'      => trim($_POST['type'] ?? ''),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'balance'   => (float)($_POST['balance'] ?? 0.00)
            ];

            if (empty($data['code']) || empty($data['name']) || empty($data['type'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية.');
                $this->redirect('account/create');
            }

            if ($this->accountModel->codeExists($data['code'])) {
                $this->setFlash('error', 'رقم الحساب مسجل مسبقاً.');
                $this->redirect('account/create');
            }

            if ($this->accountModel->createAccount($data)) {
                $this->setFlash('success', 'تم إنشاء الحساب بنجاح.');
                $this->redirect('account/tree');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الحساب.');
                $this->redirect('account/create');
            }
        } else {
            $parents = $this->accountModel->getParentAccounts();
            $data = [
                'title' => 'إضافة حساب مالي', 
                'parents' => $parents,
                'breadcrumb' => [
                    ['label' => 'شجرة الحسابات', 'url' => 'account/tree'],
                    ['label' => 'إضافة حساب', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('account/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('account/tree');
        
        $accId = (int)$id;
        $account = $this->accountModel->findById($accId);

        if (!$account) {
            $this->setFlash('error', 'الحساب المالي غير موجود.');
            $this->redirect('account/tree');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'code'      => trim($_POST['code'] ?? ''),
                'name'      => trim($_POST['name'] ?? ''),
                'type'      => trim($_POST['type'] ?? ''),
                'parent_id' => !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null,
                'balance'   => (float)($_POST['balance'] ?? 0.00)
            ];

            if (empty($data['code']) || empty($data['name'])) {
                $this->setFlash('error', 'اسم وكود الحساب مطلوبان.');
                $this->redirect('account/edit/' . $accId);
            }

            if ($this->accountModel->codeExists($data['code'], $accId)) {
                $this->setFlash('error', 'الكود مستخدم في حساب آخر.');
                $this->redirect('account/edit/' . $accId);
            }

            if ($this->accountModel->updateAccount($accId, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات الحساب بنجاح.');
                $this->redirect('account/tree');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('account/edit/' . $accId);
            }
        } else {
            $parents = $this->accountModel->getParentAccounts();
            $data = [
                'title' => 'تعديل الحساب المالي', 
                'account' => $account, 
                'parents' => $parents,
                'breadcrumb' => [
                    ['label' => 'شجرة الحسابات', 'url' => 'account/tree'],
                    ['label' => 'تعديل حساب', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('account/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin'); 
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->accountModel->deleteAccount((int)$id)) {
                    $this->setFlash('success', 'تم حذف الحساب بنجاح.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف الحساب لارتباطه بقيود أو عمليات سابقة.');
            }
        }
        $this->redirect('account/tree');
    }
}