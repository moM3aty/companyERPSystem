<?php
// المسار: app/controllers/AccountController.php

class AccountController extends Controller {
    
    private Account $accountModel;

    public function __construct() {
        // حماية الوصول: فقط من لديهم صلاحيات إدارية أو محاسبية
        $this->requireAnyRole(['admin', 'editor']);
        $this->accountModel = $this->model('Account');
    }

    /**
     * عرض دليل الحسابات (شجرة الحسابات)
     */
    public function tree(): void {
        $accounts = $this->accountModel->getChartOfAccounts();
        
        $data = [
            'title' => 'دليل الحسابات',
            'accounts' => $accounts,
            'breadcrumb' => [
                ['label' => 'المالية والمحاسبة', 'url' => '#'],
                ['label' => 'دليل الحسابات', 'url' => 'account/tree']
            ]
        ];
        
        ob_start();
        $this->view('account/tree', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * إضافة حساب جديد
     */
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
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية (رقم الحساب، الاسم، والنوع).');
                $this->redirect('account/create');
            }

            if ($this->accountModel->codeExists($data['code'])) {
                $this->setFlash('error', 'رقم الحساب (' . $data['code'] . ') مسجل مسبقاً، يرجى اختيار رقم آخر.');
                $this->redirect('account/create');
            }

            if ($this->accountModel->createAccount($data)) {
                $this->setFlash('success', 'تم تسجيل الحساب الجديد في الدليل بنجاح.');
                $this->redirect('account/tree');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الحساب.');
                $this->redirect('account/create');
            }
        } else {
            $parentAccounts = $this->accountModel->getParentAccounts();
            
            $data = [
                'title' => 'إضافة حساب جديد',
                'parents' => $parentAccounts,
                'breadcrumb' => [
                    ['label' => 'دليل الحسابات', 'url' => 'account/tree'],
                    ['label' => 'إضافة حساب', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('account/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }
}