<?php
// app/controllers/SalesInvoiceController.php

class SalesInvoiceController extends Controller {
    
    private $siModel;
    private $soModel;
    private $customerModel;
    private $productModel;

    public function __construct() {
        $this->requireAuth();
        $this->siModel = $this->model('SalesInvoice');
        if (file_exists('../app/models/SalesOrder.php')) $this->soModel = $this->model('SalesOrder');
        if (file_exists('../app/models/Customer.php')) $this->customerModel = $this->model('Customer');
        if (file_exists('../app/models/Product.php')) $this->productModel = $this->model('Product');
    }

    public function index() {
        $invoices = $this->siModel->getAllInvoices();
        $data = [
            'title' => 'فواتير المبيعات الضريبية (Sales Invoices)',
            'invoices' => $invoices,
            'breadcrumb' => [['label' => 'المبيعات', 'url' => '#'], ['label' => 'الفواتير', 'url' => 'salesInvoice/index']]
        ];
        ob_start(); $this->view('salesInvoice/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create($soId = '') {
        $soData = null;
        $soItems = [];
        
        // استيراد بيانات من أمر البيع
        if (!empty($soId) && is_numeric($soId) && $this->soModel) {
            $soData = $this->soModel->getSOById((int)$soId);
            $soItems = $this->soModel->getSOItems((int)$soId);
        }

        if ($this->isPost()) {
            $data = [
                'invoice_number' => trim($_POST['invoice_number'] ?? 'INV-'.time()),
                'customer_id'    => (int)($_POST['customer_id'] ?? 0),
                'so_id'          => !empty($_POST['so_id']) ? (int)$_POST['so_id'] : null,
                'invoice_date'   => trim($_POST['invoice_date'] ?? date('Y-m-d')),
                'due_date'       => trim($_POST['due_date'] ?? date('Y-m-d')),
                'notes'          => trim($_POST['notes'] ?? '')
            ];

            $productIds = $_POST['product_id'] ?? [];
            $descriptions = $_POST['item_description'] ?? [];
            $quantities = $_POST['item_quantity'] ?? [];
            $prices = $_POST['item_price'] ?? [];
            $discounts = $_POST['item_discount'] ?? [];
            $taxRates = $_POST['item_tax'] ?? [];
            
            $items = [];
            $subtotal = 0; $totalTax = 0; $totalDiscount = 0;

            for ($i = 0; $i < count($descriptions); $i++) {
                if (!empty(trim($descriptions[$i]))) {
                    $qty = (float)($quantities[$i] ?? 1);
                    $price = (float)($prices[$i] ?? 0);
                    $disc = (float)($discounts[$i] ?? 0);
                    $taxR = (float)($taxRates[$i] ?? 15);
                    
                    $itemSubtotal = ($qty * $price) - $disc;
                    $itemTax = $itemSubtotal * ($taxR / 100);
                    
                    $subtotal += ($qty * $price);
                    $totalDiscount += $disc;
                    $totalTax += $itemTax;
                    
                    $items[] = [
                        'product_id' => !empty($productIds[$i]) ? (int)$productIds[$i] : null,
                        'description' => trim($descriptions[$i]),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'discount' => $disc,
                        'tax_rate' => $taxR,
                        'subtotal' => $itemSubtotal + $itemTax
                    ];
                }
            }

            $data['subtotal'] = $subtotal;
            $data['discount'] = $totalDiscount;
            $data['tax_amount'] = $totalTax;
            $data['grand_total'] = ($subtotal - $totalDiscount) + $totalTax;

            if (empty($data['customer_id']) || empty($items)) {
                $this->setFlash('error', 'يجب تحديد العميل وإضافة صنف واحد على الأقل.');
            } else {
                $invoiceId = $this->siModel->createInvoice($data, $items);
                if ($invoiceId) {
                    $this->setFlash('success', 'تم إصدار الفاتورة، خصم المخزون، وتسجيل القيد المحاسبي بنجاح.');
                    $this->redirect('salesInvoice/show/' . $invoiceId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        $customers = $this->customerModel ? $this->customerModel->getAllCustomers() : [];
        $products = $this->productModel ? $this->productModel->getAllProducts() : [];

        $data = [
            'title' => 'إصدار فاتورة مبيعات ضريبية',
            'customers' => $customers,
            'products' => $products,
            'so_data' => $soData,
            'so_items' => $soItems,
            'auto_inv_num' => 'INV-' . date('Ymd') . '-' . rand(10,99)
        ];
        ob_start(); $this->view('salesInvoice/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('salesInvoice/index');
        
        $invoice = $this->siModel->getInvoiceById((int)$id);
        if (!$invoice) $this->redirect('salesInvoice/index');

        $items = $this->siModel->getInvoiceItems((int)$id);
        
        $data = [
            'title' => 'فاتورة مبيعات #' . $invoice->invoice_number,
            'invoice' => $invoice,
            'items' => $items,
            'breadcrumb' => [['label' => 'فواتير المبيعات', 'url' => 'salesInvoice/index'], ['label' => 'عرض وطباعة', 'url' => '#']]
        ];
        
        ob_start(); $this->view('salesInvoice/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->siModel->deleteInvoice((int)$id);
            $this->setFlash('success', 'تم مسح الفاتورة واسترداد المخزون وإلغاء المديونية.');
        }
        $this->redirect('salesInvoice/index');
    }
}