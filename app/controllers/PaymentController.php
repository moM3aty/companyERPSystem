<?php
// app/controllers/PaymentController.php

class PaymentController extends Controller {
    
    private $paymentModel;
    private $treasuryModel;

    public function __construct() {
        // 1. التأكد من تسجيل الدخول
        $this->requireAuth();
        
        // 2. فحص الصلاحيات بطريقة آمنة (بدون استخدام دوال قد تكون غير موجودة لديك)
        $role = Session::getUserRole();
        $allowedRoles = ['admin', 'super_admin', 'manager', 'accountant'];
        if (!in_array($role, $allowedRoles)) {
            $this->redirect('dashboard/index');
            exit;
        }
        
        $this->paymentModel = $this->model('Payment');
        $this->treasuryModel = $this->model('Treasury');
    }

    public function index() {
        $payments = [];
        try {
            $payments = $this->paymentModel->getAllPayments();
        }  catch (Throwable $e) {
    $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }

        $data = [
            'title' => 'سندات الصرف والقبض',
            'payments' => is_array($payments) ? $payments : [],
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'السندات', 'url' => 'payment/index']]
        ];
        ob_start(); $this->view('payment/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'voucher_number' => trim($_POST['voucher_number'] ?? 'PAY-'.time()),
                'payment_type'   => trim($_POST['payment_type'] ?? 'Out'), 
                'treasury_id'    => (int)($_POST['treasury_id'] ?? 0),
                'supplier_id'    => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'customer_id'    => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'invoice_id'     => !empty($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : null,
                'payment_date'   => trim($_POST['payment_date'] ?? date('Y-m-d')),
                'amount'         => (float)($_POST['amount'] ?? 0),
                'payment_method' => trim($_POST['payment_method'] ?? 'Cash'),
                'reference_number'=> trim($_POST['reference_number'] ?? ''),
                'notes'          => trim($_POST['notes'] ?? ''),
                'attachment'     => null
            ];

            // التأكد من وجود رصيد كافٍ في الخزنة عند الصرف
            try {
                $treasury = $this->treasuryModel->getTreasuryById($data['treasury_id']);
                if ($data['payment_type'] == 'Out' && $treasury && $treasury->current_balance < $data['amount']) {
                    $this->setFlash('error', 'الرصيد في هذا الصندوق غير كافٍ لإتمام سند الصرف.');
                    $this->redirect('payment/create');
                    return;
                }
            } catch (Throwable $e) {
    $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }

            // رفع المرفقات
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/payments/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['attachment'] = $fileName;
                }
            }

            try {
                $paymentId = $this->paymentModel->createPayment($data);
                if ($paymentId) {
                    $this->setFlash('success', 'تم إصدار السند بنجاح وتحديث الأرصدة.');
                    $this->redirect('payment/show/' . $paymentId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ في قاعدة البيانات.');
                }
            }catch (Throwable $e) {
    $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }
        
        }

        // جلب البيانات بشكل آمن لعدم التسبب بخطأ 500 في الـ View
        $db = Database::getInstance();
        $cid = Session::get('company_id') ?: 1;
        
        $treasuries = []; $suppliers = []; $customers = [];
        
        try {
            $db->query("SELECT id, name, current_balance FROM treasuries WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $res = $db->resultSet();
            $treasuries = is_array($res) ? $res : [];
        } catch (Throwable $e) {
            $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }

        try {
            $db->query("SELECT id, company_name as name FROM suppliers WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $res = $db->resultSet();
            $suppliers = is_array($res) ? $res : [];
        } catch (Throwable $e) {
            $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }

        try {
            $db->query("SELECT id, name FROM customers WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $res = $db->resultSet();
            $customers = is_array($res) ? $res : [];
        } catch (Throwable $e) {
            $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }

        $data = [
            'title' => 'إصدار سند مالي',
            'treasuries' => $treasuries,
            'suppliers' => $suppliers,
            'customers' => $customers,
            'auto_pay_num' => 'PAY-' . date('Ymd') . '-' . rand(100,999)
        ];
        
        ob_start(); $this->view('payment/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('payment/index');
        
        $payment = null;
        try {
            $payment = $this->paymentModel->getPaymentById((int)$id);
        } catch (Throwable $e) {
            $this->setFlash('error', 'خطأ تقني: ' . $e->getMessage());
        }
        
        if (!$payment) {
            $this->setFlash('error', 'السند غير موجود.');
            $this->redirect('payment/index');
        }

        $data = [
            'title' => 'سند #' . $payment->voucher_number,
            'payment' => $payment,
            'breadcrumb' => [['label' => 'السندات', 'url' => 'payment/index'], ['label' => 'عرض', 'url' => '#']]
        ];
        ob_start(); $this->view('payment/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
    public function delete($id = '') {
        // حماية أمنية: الإدارة العليا فقط من يحق لها حذف السندات
        $this->requireAnyRole(['admin', 'super_admin']); 
        
        if ($this->isPost() && !empty($id)) {
            if ($this->paymentModel->deletePayment((int)$id)) {
                $this->setFlash('success', 'تم حذف السند نهائياً واسترجاع الأرصدة (الخزنة والمديونيات) بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة حذف السند.');
            }
        }
        $this->redirect('payment/index');
    }
}