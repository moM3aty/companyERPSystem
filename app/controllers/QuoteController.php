<?php
// app/controllers/QuoteController.php

class QuoteController extends Controller {
    
    private Quote $quoteModel;

    public function __construct() {
        $this->requireAuth();
        $this->quoteModel = $this->model('Quote');
    }

    public function index(): void {
        $quotes = $this->quoteModel->getAllQuotes();
        
        $data = [
            'title' => 'عروض الأسعار (Quotations)',
            'quotes' => $quotes,
            'breadcrumb' => [
                ['label' => 'المبيعات', 'url' => '#'],
                ['label' => 'عروض الأسعار', 'url' => 'quote/index']
            ]
        ];
        
        ob_start();
        $this->view('quotes/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $items = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['unit_price'] ?? [];
            
            if (!$customerId || empty($items)) {
                $this->setFlash('error', 'يجب اختيار العميل وإضافة صنف واحد على الأقل');
                $this->redirect('quote/create');
            }

            $totalAmount = 0.0;
            $quoteItems = [];
            foreach ($items as $index => $prodId) {
                $qty = (int)($quantities[$index] ?? 1);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $quoteItems[] = [
                        'product_id' => (int)$prodId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $quoteData = [
                'customer_id' => $customerId,
                'total_amount' => $totalAmount
            ];
            
            if ($this->quoteModel->createQuote($quoteData, $quoteItems)) {
                $this->setFlash('success', 'تم إنشاء عرض السعر بنجاح.');
                $this->redirect('quote/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ عرض السعر.');
                $this->redirect('quote/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query('SELECT id, name FROM customers ORDER BY name ASC');
            $customers = $db->resultSet();
            
            $db->query('SELECT id, name, price, quantity FROM products ORDER BY name ASC');
            $products = $db->resultSet();

            $data = [
                'title' => 'إنشاء عرض سعر جديد',
                'customers' => $customers,
                'products' => $products,
                'breadcrumb' => [['label' => 'عروض الأسعار', 'url' => 'quote/index'], ['label' => 'جديد', 'url' => '#']]
            ];
            
            ob_start();
            $this->view('quotes/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('quote/index');
        
        $quoteId = (int)$id;
        $quote = $this->quoteModel->getQuoteById($quoteId);
        
        if (!$quote) {
            $this->setFlash('error', 'عرض السعر غير موجود.');
            $this->redirect('quote/index');
        }

        $items = $this->quoteModel->getQuoteItems($quoteId);
        
        $data = [
            'title' => 'تفاصيل وطباعة عرض السعر',
            'quote' => $quote,
            'items' => $items,
            'breadcrumb' => [['label' => 'عروض الأسعار', 'url' => 'quote/index'], ['label' => 'تفاصيل العرض', 'url' => '#']]
        ];
        
        ob_start();
        $this->view('quotes/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    
    public function changeStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $status = trim($_POST['status'] ?? '');
            if (in_array($status, ['draft', 'sent', 'accepted', 'rejected'])) {
                $this->quoteModel->updateStatus((int)$id, $status);
                $this->setFlash('success', 'تم تحديث حالة عرض السعر.');
            }
        }
        $this->redirect('quote/show/' . $id);
    }
}