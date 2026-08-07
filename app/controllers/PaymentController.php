<?php
// app/controllers/PaymentController.php

class PaymentController extends Controller {
    
    private Payment $paymentModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        $this->paymentModel = $this->model('Payment');
    }

    public function index(): void {
        $payments = $this->paymentModel->getAllPayments();
        
        $data = [
            'title' => 'سجل المقبوضات والمدفوعات',
            'payments' => $payments,
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => '#'],
                ['label' => 'المدفوعات', 'url' => 'payment/index']
            ]
        ];
        
        ob_start();
        $this->view('payments/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function createReceipt(): void {
        if ($this->isPost()) {
            $data = [
                'reference_id'   => (int)($_POST['invoice_id'] ?? 0),
                'reference_type' => 'invoice',
                'amount'         => (float)($_POST['amount'] ?? 0),
                'payment_method' => trim($_POST['payment_method'] ?? 'cash'),
                'notes'          => trim($_POST['notes'] ?? '')
            ];

            if ($data['reference_id'] > 0 && $data['amount'] > 0) {
                if ($this->paymentModel->createPayment($data)) {
                    $this->setFlash('success', 'تم تسجيل سند القبض بنجاح، وتم إنشاء القيد المحاسبي وتحديث أرصدة العميل والخزنة تلقائياً.');
                    $this->redirect('payment/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ سند القبض وربطه بالنظام المحاسبي.');
                }
            } else {
                $this->setFlash('error', 'الرجاء اختيار الفاتورة وإدخال مبلغ صحيح.');
            }
        }
        
        $db = Database::getInstance();
        $db->query("SELECT i.id, i.invoice_number, c.name as customer_name, i.total_amount 
                    FROM invoices i 
                    JOIN customers c ON i.customer_id = c.id 
                    ORDER BY i.created_at DESC LIMIT 100");
        $invoices = $db->resultSet();

        $data = ['title' => 'تسجيل سند قبض (من عميل)', 'invoices' => $invoices];
        ob_start(); $this->view('payments/create_receipt', $data); $content = ob_get_clean(); Layout::render($content, $data);
    }

    public function createPayment(): void {
        if ($this->isPost()) {
            $data = [
                'reference_id'   => (int)($_POST['po_id'] ?? 0),
                'reference_type' => 'purchase_order',
                'amount'         => (float)($_POST['amount'] ?? 0),
                'payment_method' => trim($_POST['payment_method'] ?? 'bank_transfer'),
                'notes'          => trim($_POST['notes'] ?? '')
            ];

            if ($data['reference_id'] > 0 && $data['amount'] > 0) {
                if ($this->paymentModel->createPayment($data)) {
                    $this->setFlash('success', 'تم تسجيل سند الصرف بنجاح، وتم إنشاء القيد وتخفيض مديونية المورد وتخفيض رصيد البنك تلقائياً.');
                    $this->redirect('payment/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ سند الصرف.');
                }
            } else {
                $this->setFlash('error', 'الرجاء اختيار أمر الشراء وإدخال مبلغ صحيح.');
            }
        }
        
        $db = Database::getInstance();
        $db->query("SELECT po.id, po.po_number, s.name as supplier_name, po.total_amount 
                    FROM purchase_orders po 
                    JOIN suppliers s ON po.supplier_id = s.id 
                    WHERE po.status IN ('approved', 'ordered', 'delivered')
                    ORDER BY po.created_at DESC LIMIT 100");
        $pos = $db->resultSet();

        $data = ['title' => 'تسجيل سند صرف (لمورد)', 'pos' => $pos];
        ob_start(); $this->view('payments/create_payment', $data); $content = ob_get_clean(); Layout::render($content, $data);
    }
}