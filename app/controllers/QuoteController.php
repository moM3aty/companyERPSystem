<?php
// app/controllers/QuoteController.php

class QuoteController extends Controller {
    
    private $quoteModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'sales', 'accountant'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->quoteModel = $this->model('Quote');
    }

    public function index() {
        $quotes = [];
        try {
            $quotes = $this->quoteModel->getAllQuotes();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'عروض الأسعار (Quotations)',
            'quotes' => is_array($quotes) ? $quotes : [],
            'breadcrumb' => [['label' => 'المبيعات', 'url' => '#'], ['label' => 'عروض الأسعار', 'url' => 'quote/index']]
        ];
        
        ob_start(); $this->view('quote/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'quote_number' => trim($_POST['quote_number'] ?? ''),
                'customer_id'  => (int)($_POST['customer_id'] ?? 0),
                'lead_id'      => (int)($_POST['lead_id'] ?? 0),
                'quote_date'   => trim($_POST['quote_date'] ?? date('Y-m-d')),
                'expiry_date'  => trim($_POST['expiry_date'] ?? ''),
                'total_amount' => (float)($_POST['grand_total'] ?? 0),
                'notes'        => trim($_POST['notes'] ?? '')
            ];

            // 🟢 استقبال product_id 🟢
            $productIds   = $_POST['product_id'] ?? [];
            $productNames = $_POST['product_name'] ?? [];
            $quantities   = $_POST['quantity'] ?? [];
            $prices       = $_POST['unit_price'] ?? [];
            $totals       = $_POST['total_price'] ?? [];

            $items = [];
            for ($i = 0; $i < count($productIds); $i++) {
                if (!empty($productIds[$i]) && $quantities[$i] > 0) {
                    $items[] = [
                        'product_id'   => (int)$productIds[$i],
                        'product_name' => $productNames[$i] ?? 'منتج',
                        'quantity'     => (float)$quantities[$i],
                        'unit_price'   => (float)$prices[$i],
                        'total_price'  => (float)$totals[$i]
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'يجب اختيار صنف واحد على الأقل من المخزون.');
                $this->redirect('quote/create');
                return;
            }

            if (empty($data['customer_id']) && empty($data['lead_id'])) {
                $this->setFlash('error', 'يرجى اختيار عميل حالي أو عميل محتمل لتوجيه عرض السعر له.');
                $this->redirect('quote/create');
                return;
            }

            try {
                $quoteId = $this->quoteModel->createQuote($data, $items);
                if ($quoteId) {
                    $this->setFlash('success', 'تم حفظ عرض السعر بنجاح.');
                    $this->redirect('quote/show/' . $quoteId);
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', 'تفاصيل الخطأ التقني: ' . $e->getMessage());
            }
        }

        $db = Database::getInstance();
        $cid = Session::get('company_id') ?: 1;
        $customers = []; $leads = []; $products = [];
        
        try {
            $db->query("SELECT id, name FROM customers WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $customers = $db->resultSet() ?: [];
        } catch (Throwable $e) {}

        try {
            $db->query("SELECT id, name FROM leads WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $leads = $db->resultSet() ?: [];
        } catch (Throwable $e) {}

        // 🟢 جلب المنتجات لتظهر في القائمة المنسدلة 🟢
        try {
            $db->query("SELECT id, name, sell_price as price FROM products WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $products = $db->resultSet() ?: [];
        } catch (Throwable $e) {
            // لو كان حقل السعر اسمه price بدلاً من sell_price
            try {
                $db->query("SELECT id, name, price FROM products WHERE company_id = :cid");
                $db->bind(':cid', $cid);
                $products = $db->resultSet() ?: [];
            } catch(Throwable $t) {}
        }

        $data = [
            'title' => 'إنشاء عرض سعر جديد',
            'customers' => $customers,
            'leads' => $leads,
            'products' => $products,
            'auto_quote_num' => 'QT-' . date('Ymd') . '-' . rand(100, 999)
        ];
        
        ob_start(); $this->view('quote/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('quote/index');
        
        $quote = null;
        try { $quote = $this->quoteModel->getQuoteById((int)$id); } catch (Throwable $e) {}
        
        if (!$quote) {
            $this->setFlash('error', 'عرض السعر غير موجود.');
            $this->redirect('quote/index');
        }
        
        $items = $this->quoteModel->getQuoteItems($quote->id);

        $data = [
            'title' => 'عرض سعر #' . $quote->quote_number,
            'quote' => $quote,
            'items' => $items,
            'breadcrumb' => [['label' => 'المبيعات', 'url' => 'quote/index'], ['label' => 'عرض السعر', 'url' => '#']]
        ];
        
        ob_start(); $this->view('quote/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin', 'manager']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->quoteModel->deleteQuote((int)$id)) {
                $this->setFlash('success', 'تم حذف عرض السعر بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
            }
        }
        $this->redirect('quote/index');
    }
}