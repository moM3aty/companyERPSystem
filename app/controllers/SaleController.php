<?php
// app/controllers/SaleController.php

class SaleController extends Controller {
    
    /** @var Sale */
    private Sale $saleModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->saleModel = $this->model('Sale');
    }

    /**
     * عرض صفحة سجل الفواتير
     */
    public function index(): void {
        $invoices = $this->saleModel->getAllInvoices();
        
        $data = [
            'title' => 'المبيعات والفواتير',
            'invoices' => $invoices,
            'flash' => $this->getFlash()
        ];
        
        $this->view('sales/index', $data);
    }

    /**
     * عرض وحفظ نموذج إنشاء الفاتورة (POS)
     */
    public function create(): void {
        if ($this->isPost()) {
            // تنظيف المدخلات
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerName = trim($_POST['customer_name'] ?? 'عميل نقدي');
            $products = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            // تحقق من وجود منتجات
            if (empty($products)) {
                $this->setFlash('error', 'يجب إضافة منتج واحد على الأقل للفاتورة.');
                $this->redirect('sale/create');
            }

            $totalAmount = 0.0;
            $items = [];
            
            // تجميع وتجهيز بيانات الأصناف
            foreach ($products as $i => $pid) {
                $q = (int)($quantities[$i] ?? 0);
                $p = (float)($prices[$i] ?? 0);
                
                if ($q > 0 && $p >= 0) {
                    $sub = $q * $p;
                    $totalAmount += $sub;
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity' => $q,
                        'price' => $p,
                        'subtotal' => $sub
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'بيانات المنتجات والكميات غير صحيحة.');
                $this->redirect('sale/create');
            }

            // تنفيذ الحفظ عبر المودل
            if ($this->saleModel->createInvoice($customerName, $totalAmount, $items)) {
                $this->setFlash('success', 'تم إصدار الفاتورة بنجاح وخصم الكميات من المخزون.');
                $this->redirect('sale/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء إصدار الفاتورة. تأكد من توفر الكمية الكافية في المخزون.');
                $this->redirect('sale/create');
            }
        } else {
            // جلب المنتجات المتوفرة فقط (الكمية أكبر من صفر) لاستخدامها في واجهة البيع
            $db = Database::getInstance();
            $db->query('SELECT id, name, price, quantity FROM products WHERE quantity > 0 ORDER BY name ASC');
            $availableProducts = $db->resultSet();
            
            $data = [
                'title' => 'إنشاء فاتورة مبيعات',
                'products' => $availableProducts,
                'flash' => $this->getFlash()
            ];
            
            $this->view('sales/create', $data);
        }
    }

    /**
     * عرض تفاصيل وطباعة الفاتورة
     * @param string $id
     */
    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('sale/index');
        }

        $invoiceId = (int)$id;
        $invoice = $this->saleModel->getInvoiceById($invoiceId);
        
        if (!$invoice) {
            $this->setFlash('error', 'الفاتورة المطلوبة غير موجودة في النظام.');
            $this->redirect('sale/index');
        }

        $items = $this->saleModel->getInvoiceItems($invoiceId);
        
        $data = [
            'title' => 'تفاصيل الفاتورة',
            'invoice' => $invoice,
            'items' => $items,
            'flash' => $this->getFlash()
        ];
        
        // يعتمد على ملف العرض sales/view.php
        $this->view('sales/view', $data);
    }

    public function commissions(): void {
        $db = Database::getInstance();
        
        // استعلام ذكي يجمع المبيعات لكل مندوب ويحسب العمولة (مثلاً 5%)
        $sql = "SELECT u.name as rep_name, 
                       COUNT(i.id) as total_invoices, 
                       SUM(i.total_amount) as total_sales,
                       (SUM(i.total_amount) * 0.05) as estimated_commission
                FROM invoices i
                JOIN users u ON i.sales_rep_id = u.id
                GROUP BY u.id
                ORDER BY total_sales DESC";
                
        $db->query($sql);
        $commissions = $db->resultSet();

        $data = [
            'title' => 'تقرير عمولات المبيعات',
            'commissions' => $commissions,
            'flash' => $this->getFlash()
        ];

        // سنرسلها للفيو الخاص بالتقارير
        ob_start();
        $this->view('sales/commissions', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}