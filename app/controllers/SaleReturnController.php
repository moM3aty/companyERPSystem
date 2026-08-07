<?php
// المسار: app/controllers/SaleReturnController.php

class SaleReturnController extends Controller {
    
    private SaleReturn $returnModel;

    public function __construct() {
        $this->requireAuth();
        $this->returnModel = $this->model('SaleReturn');
    }

    public function index(): void {
        $returns = $this->returnModel->getAllReturns();
        
        $data = [
            'title' => 'سجل المرتجعات',
            'returns' => $returns
        ];
        
        ob_start();
        $this->view('sales_returns/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $invoiceId = !empty($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;
            $reason = trim($_POST['reason'] ?? '');
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            if ($invoiceId === 0 || empty($productIds)) {
                $this->setFlash('error', 'يجب اختيار الفاتورة وتحديد صنف واحد على الأقل للترجيع.');
                $this->redirect('saleReturn/create');
            }

            $items = [];
            $totalRefund = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                
                if ($qty > 0) {
                    $subtotal = $qty * $price;
                    $totalRefund += $subtotal;
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'بيانات الأصناف غير صالحة.');
                $this->redirect('saleReturn/create');
            }

            $returnData = [
                'invoice_id' => $invoiceId,
                'total_refund' => $totalRefund,
                'reason' => $reason
            ];

            if ($this->returnModel->createSaleReturn($returnData, $items)) {
                $this->setFlash('success', 'تم معالجة المرتجع وإعادة الكميات للمخزون بنجاح.');
                $this->redirect('saleReturn/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء معالجة المرتجع.');
                $this->redirect('saleReturn/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query('SELECT id, invoice_number, customer_name FROM invoices ORDER BY created_at DESC LIMIT 50');
            $invoices = $db->resultSet();
            
            $db->query('SELECT id, name, price FROM products');
            $products = $db->resultSet();
            
            $data = [
                'title' => 'تسجيل مرتجع مبيعات',
                'invoices' => $invoices,
                'products' => $products
            ];
            
            ob_start();
            $this->view('sales_returns/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}