<?php
// app/controllers/SaleController.php
class SaleController extends Controller {
    private Sale $saleModel;

    public function __construct() {
        $this->requireAuth();
        $this->saleModel = $this->model('Sale');
    }

    public function index(): void {
        $invoices = $this->saleModel->getAllInvoices();
        $data = ['title' => 'فواتير المبيعات', 'invoices' => $invoices];
        ob_start();
        $this->view('sales/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null;
            $customerName = trim($_POST['customer_name'] ?? 'عميل نقدي');
            
            $products = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            if (empty($products)) {
                $this->setFlash('error', 'يجب إضافة منتج واحد على الأقل للفاتورة.');
                $this->redirect('sale/create');
            }

            $totalAmount = 0.0;
            $items = [];
            foreach ($products as $i => $pid) {
                $q = (int)($quantities[$i] ?? 0);
                $p = (float)($prices[$i] ?? 0);
                if ($q > 0 && $p >= 0) {
                    $sub = $q * $p;
                    $totalAmount += $sub;
                    $items[] = ['product_id' => (int)$pid, 'quantity' => $q, 'price' => $p, 'subtotal' => $sub];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'بيانات المنتجات والكميات غير صحيحة.');
                $this->redirect('sale/create');
            }
            
            $invoiceData = [
                'customer_id' => $customerId,
                'customer_name' => $customerName,
                'total_amount' => $totalAmount
            ];

            // الموديل سيقوم بخصم المخزون وإنشاء القيد المحاسبي للمبيعات وتحديث رصيد العميل
            if ($this->saleModel->createInvoice($invoiceData, $items)) {
                $this->setFlash('success', 'تم إصدار الفاتورة بنجاح. تم خصم المخزون وتوليد القيد المحاسبي الآلي للإيرادات.');
                $this->redirect('sale/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء إصدار الفاتورة. تأكد من توفر الكمية الكافية في المخزون.');
                $this->redirect('sale/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query('SELECT id, name, price, quantity FROM products WHERE quantity > 0 ORDER BY name ASC');
            $availableProducts = $db->resultSet();
            $db->query('SELECT id, name FROM customers ORDER BY name ASC');
            $customers = $db->resultSet();
            
            $data = ['title' => 'إصدار فاتورة مبيعات جديدة', 'products' => $availableProducts, 'customers' => $customers];
            ob_start();
            $this->view('sales/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('sale/index');
        
        $invoice = $this->saleModel->getInvoiceById((int)$id);
        if (!$invoice) {
            $this->setFlash('error', 'الفاتورة المطلوبة غير موجودة.');
            $this->redirect('sale/index');
        }

        $items = $this->saleModel->getInvoiceItems((int)$id);
        $data = ['title' => 'تفاصيل الفاتورة', 'invoice' => $invoice, 'items' => $items];
        
        ob_start();
        $this->view('sales/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
    
    public function commissions(): void {
        $this->requireAnyRole(['admin', 'manager']);
        
        $commissionRate = 0.05; // 5% افتراضياً ويمكن جلبها من الإعدادات لاحقاً
        $commissions = $this->saleModel->getSalesCommissions($commissionRate);
        
        $data = [
            'title' => 'عمولات المبيعات (المندوبين)',
            'commissions' => $commissions
        ];
        
        ob_start();
        $this->view('sales/commissions', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}