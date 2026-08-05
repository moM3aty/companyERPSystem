<?php
// app/controllers/QuoteController.php

class QuoteController extends Controller {
    
    public function __construct() {
        // حماية القسم ليسمح فقط للمستخدمين المسجلين بالدخول
        $this->requireAuth();
    }

    // عرض جميع عروض الأسعار (Quotations)
    public function index() {
        $db = Database::getInstance();
        $db->query('
            SELECT q.*, c.name as customer_name 
            FROM quotes q
            LEFT JOIN customers c ON q.customer_id = c.id
            ORDER BY q.id DESC
        ');
        $quotes = $db->resultSet();
        
        $data = [
            'title' => 'عروض الأسعار',
            'quotes' => $quotes,
            'flash' => $this->getFlash()
        ];
        
        // سنقوم ببرمجة الواجهة الخاصة بها في الدفعة القادمة
        $this->view('quotes/index', $data);
    }

    // إنشاء عرض سعر جديد
    public function create() {
        $db = Database::getInstance();

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

            // حساب الإجمالي
            $totalAmount = 0;
            $quoteItems = [];
            foreach ($items as $index => $prodId) {
                $qty = (int)($quantities[$index] ?? 1);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $quoteItems[] = [
                        'product_id' => $prodId,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $quoteNumber = 'QTE-' . date('Ym') . '-' . rand(1000, 9999);
            
            // حفظ العرض في قاعدة البيانات
            $db->query('
                INSERT INTO quotes (quote_number, customer_id, total_amount, status, created_by)
                VALUES (:qnum, :cid, :total, "draft", :uid)
            ');
            $db->bind(':qnum', $quoteNumber);
            $db->bind(':cid', $customerId, PDO::PARAM_INT);
            $db->bind(':total', $totalAmount);
            $db->bind(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
            
            if ($db->execute()) {
                // ملاحظة: في تطبيق حقيقي سنقوم هنا بحفظ عناصر العرض ($quoteItems) في جدول quote_items
                $this->setFlash('success', 'تم إنشاء عرض السعر بنجاح برقم ' . $quoteNumber);
                $this->redirect('quote/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ عرض السعر');
                $this->redirect('quote/create');
            }
        } else {
            // جلب البيانات اللازمة للنموذج
            $db->query('SELECT id, name FROM customers ORDER BY name ASC');
            $customers = $db->resultSet();
            
            $db->query('SELECT id, name, price, quantity FROM products ORDER BY name ASC');
            $products = $db->resultSet();

            $data = [
                'title' => 'إنشاء عرض سعر جديد',
                'customers' => $customers,
                'products' => $products,
                'flash' => $this->getFlash()
            ];
            
            // سنقوم ببرمجة الواجهة الخاصة بها في الدفعة القادمة
            $this->view('quotes/create', $data);
        }
    }
}