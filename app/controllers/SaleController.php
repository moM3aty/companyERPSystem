<?php
// app/controllers/SaleController.php

class SaleController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول تلقائياً
        $this->requireAuth();
    }

    // عرض قائمة الفواتير
    public function index() {
        $saleModel = $this->model('Sale');
        $data = [
            'title' => 'الفواتير والمبيعات',
            'invoices' => $saleModel->getInvoices(),
            'flash' => $this->getFlash()
        ];
        $this->view('sales/index', $data);
    }

    // إنشاء فاتورة جديدة
    public function create() {
        $productModel = $this->model('Product');
        $saleModel = $this->model('Sale');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerName = trim($_POST['customer_name']);
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            $items = [];
            $totalAmount = 0;

            // تجميع البيانات وحساب الإجمالي
            for ($i = 0; $i < count($productIds); $i++) {
                if (!empty($productIds[$i]) && isset($quantities[$i]) && $quantities[$i] > 0) {
                    // التحقق من توفر المنتج
                    $product = $productModel->getProductById($productIds[$i]);
                    if (!$product) {
                        $this->setFlash('error', 'المنتج غير موجود');
                        $this->redirect('sale/create');
                    }
                    
                    // التحقق من الكمية المتوفرة
                    if ($quantities[$i] > $product->quantity) {
                        $this->setFlash('error', 'الكمية المطلوبة للمنتج "' . $product->name . '" تتجاوز المخزون المتوفر (' . $product->quantity . ')');
                        $this->redirect('sale/create');
                    }
                    
                    $subtotal = $quantities[$i] * $prices[$i];
                    $totalAmount += $subtotal;
                    
                    $items[] = [
                        'product_id' => $productIds[$i],
                        'quantity' => $quantities[$i],
                        'price' => $prices[$i],
                        'subtotal' => $subtotal
                    ];
                }
            }

            // حفظ الفاتورة
            if (!empty($items)) {
                if ($saleModel->createInvoice($customerName, $totalAmount, $items)) {
                    $this->setFlash('success', 'تم إنشاء الفاتورة بنجاح');
                    $this->redirect('sale/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ الفاتورة');
                    $this->redirect('sale/create');
                }
            } else {
                $this->setFlash('error', 'يرجى إضافة منتجات للفاتورة');
                $this->redirect('sale/create');
            }
            exit();
        } else {
            $data = [
                'title' => 'إنشاء فاتورة جديدة',
                'products' => $productModel->getProducts(),
                'flash' => $this->getFlash()
            ];
            $this->view('sales/create', $data);
        }
    }

    // عرض تفاصيل الفاتورة (تم تغيير الاسم من view إلى show)
    public function show($id) {
        $saleModel = $this->model('Sale');
        $invoice = $saleModel->getInvoiceById($id);
        
        if (!$invoice) {
            $this->setFlash('warning', 'الفاتورة غير موجودة');
            $this->redirect('sale/index');
        }
        
        $data = [
            'title' => 'تفاصيل الفاتورة',
            'invoice' => $invoice,
            'items' => $saleModel->getInvoiceItems($id),
            'flash' => $this->getFlash()
        ];
        $this->view('sales/view', $data);
    }
}