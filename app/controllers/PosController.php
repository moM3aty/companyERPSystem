<?php
// app/controllers/PosController.php

class PosController extends Controller {
    
    private Product $productModel;
    private Customer $customerModel;
    private Sale $saleModel;

    public function __construct() {
        $this->requireAuth();
        $this->productModel = $this->model('Product');
        $this->customerModel = $this->model('Customer');
        $this->saleModel = $this->model('Sale');
    }

    public function index(): void {
        // جلب جميع المنتجات (بدون تصفية الكمية الصفرية لتتمكن من البيع في كل الحالات)
        $products = $this->productModel->getAllProducts();
        $categories = [];
        
        foreach ($products as $p) {
            if (!empty($p->category_name) && !in_array($p->category_name, $categories)) {
                $categories[] = $p->category_name;
            }
        }

        $customers = $this->customerModel->getAllCustomers();

        $data = [
            'title' => 'نقطة البيع (POS)',
            'products' => $products,
            'categories' => $categories,
            'customers' => $customers
        ];
        
        ob_start();
        $this->view('pos/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function checkout(): void {
        if ($this->isPost()) {
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $customerName = 'عميل نقدي';
            
            if ($customerId) {
                $cust = $this->customerModel->getCustomerById($customerId);
                if ($cust) $customerName = $cust->name;
            }

            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];
            
            if (empty($productIds)) {
                $this->setFlash('error', 'السلة فارغة.');
                $this->redirect('pos/index');
                return;
            }

            $totalAmount = 0;
            $items = [];
            
            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'بيانات السلة غير صحيحة.');
                $this->redirect('pos/index');
                return;
            }

            $invoiceData = [
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'total_amount' => $totalAmount
            ];

            // 🟢 التعديل: الحصول على رقم الفاتورة للتوجه لشاشة الطباعة
            $invoiceId = $this->saleModel->createInvoice($invoiceData, $items);
            
            if ($invoiceId) {
                $this->setFlash('success', 'تمت عملية البيع بنجاح.');
                // التوجيه إلى الفاتورة لطباعتها
                $this->redirect('sale/show/' . $invoiceId);
            } else {
                $this->setFlash('error', 'فشل إتمام العملية.');
                $this->redirect('pos/index');
            }
        } else {
            $this->redirect('pos/index');
        }
    }
}