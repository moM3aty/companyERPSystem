<?php
// app/controllers/TreasuryController.php

class TreasuryController extends Controller {
    
    private $treasuryModel;

    public function __construct() {
        $this->requireAuth();
        $this->treasuryModel = $this->model('Treasury');
    }

    public function index() {
        $treasuries = $this->treasuryModel->getAllTreasuries();
        $transactions = $this->treasuryModel->getTransactions();
        
        $data = [
            'title' => 'الخزينة والبنوك',
            'treasuries' => $treasuries,
            'transactions' => $transactions,
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => '#'],
                ['label' => 'الخزينة', 'url' => 'treasury/index']
            ]
        ];
        
        ob_start();
        $this->view('treasury/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? 'cash'),
                'balance' => (float)($_POST['balance'] ?? 0)
            ];

            if (!empty($data['name'])) {
                if ($this->treasuryModel->createTreasury($data)) {
                    $this->setFlash('success', 'تم إضافة الخزنة بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الإضافة.');
                }
            } else {
                $this->setFlash('error', 'اسم الخزنة مطلوب.');
            }
        }
        $this->redirect('treasury/index');
    }

    public function delete(string $id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->treasuryModel->deleteTreasury((int)$id)) {
                    $this->setFlash('success', 'تم حذف الخزنة بنجاح.');
                } else {
                    $this->setFlash('error', 'فشل عملية الحذف.');
                }
            } catch(PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف الخزنة لوجود حركات مالية مرتبطة بها.');
            }
        }
        $this->redirect('treasury/index');
    }

    /* دالة إنشاء حركة مالية (الإيداع والسحب) التي كانت تسبب الخطأ */
    public function createTransaction() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'treasury_id' => (int)($_POST['treasury_id'] ?? 0),
                'type' => trim($_POST['type'] ?? 'deposit'),
                'amount' => (float)($_POST['amount'] ?? 0),
                'transaction_date' => trim($_POST['transaction_date'] ?? date('Y-m-d')),
                'reference' => trim($_POST['reference'] ?? ''),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            if ($data['treasury_id'] > 0 && $data['amount'] > 0) {
                // التحقق من الرصيد في حالة السحب
                if ($data['type'] === 'withdrawal') {
                    $treasury = $this->treasuryModel->getTreasuryById($data['treasury_id']);
                    if ($treasury && $treasury->balance < $data['amount']) {
                        $this->setFlash('error', 'الرصيد الحالي في الخزنة لا يكفي لعملية السحب.');
                        $this->redirect('treasury/index');
                        return;
                    }
                }

                if ($this->treasuryModel->createTransaction($data)) {
                    $this->setFlash('success', 'تم تسجيل الحركة المالية وتحديث الرصيد بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ العملية.');
                }
            } else {
                $this->setFlash('error', 'تأكد من اختيار الخزنة وإدخال مبلغ صحيح.');
            }
        }
        $this->redirect('treasury/index');
    }
}