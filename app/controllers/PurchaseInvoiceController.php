<?php
// app/controllers/PurchaseInvoiceController.php

class PurchaseInvoiceController extends Controller {
    
    private $piModel;
    private $grnModel;
    private $poModel;
    private $supplierModel;

    public function __construct() {
        $this->requireAuth();
        $this->piModel = $this->model('PurchaseInvoice');
        if (file_exists('../app/models/Grn.php')) $this->grnModel = $this->model('Grn');
        if (file_exists('../app/models/PurchaseOrder.php')) $this->poModel = $this->model('PurchaseOrder');
        if (file_exists('../app/models/Supplier.php')) $this->supplierModel = $this->model('Supplier');
    }

    public function index() {
        $invoices = $this->piModel->getAllInvoices();
        $data = [
            'title' => 'فواتير الموردين (Supplier Invoices)',
            'invoices' => $invoices,
            'breadcrumb' => [['label' => 'المالية والمشتريات', 'url' => '#'], ['label' => 'فواتير الموردين', 'url' => 'purchaseInvoice/index']]
        ];
        ob_start(); $this->view('purchaseInvoice/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create($grnId = '') {
        $grnData = null;
        $poData = null;
        $grnItems = [];
        
        // 🟢 3-Way Match Data Loading 🟢
        if (!empty($grnId) && is_numeric($grnId) && $this->grnModel) {
            $grnData = $this->grnModel->getGrnById((int)$grnId);
            $grnItems = $this->grnModel->getGrnItems((int)$grnId);
            if ($grnData && $grnData->po_id && $this->poModel) {
                $poData = $this->poModel->getPOById($grnData->po_id);
            }
        }

        if ($this->isPost()) {
            $data = [
                'invoice_number'      => trim($_POST['invoice_number'] ?? 'PI-'.time()),
                'supplier_invoice_no' => trim($_POST['supplier_invoice_no'] ?? ''),
                'supplier_id'         => (int)($_POST['supplier_id'] ?? 0),
                'po_id'               => !empty($_POST['po_id']) ? (int)$_POST['po_id'] : null,
                'grn_id'              => !empty($_POST['grn_id']) ? (int)$_POST['grn_id'] : null,
                'invoice_date'        => trim($_POST['invoice_date'] ?? date('Y-m-d')),
                'due_date'            => trim($_POST['due_date'] ?? date('Y-m-d')),
                'notes'               => trim($_POST['notes'] ?? ''),
                'match_status'        => 'Matched', // Simplified: assume user verified
                'attachment'          => null
            ];

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/invoices/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['attachment'] = $fileName;
                }
            }

            $productIds = $_POST['product_id'] ?? [];
            $descriptions = $_POST['item_description'] ?? [];
            $quantities = $_POST['item_quantity'] ?? [];
            $prices = $_POST['item_price'] ?? [];
            $discounts = $_POST['item_discount'] ?? [];
            $taxRates = $_POST['item_tax'] ?? [];
            
            $items = [];
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;

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

            if (empty($data['supplier_id']) || empty($items)) {
                $this->setFlash('error', 'يجب تحديد المورد وإضافة صنف واحد على الأقل.');
            } else {
                $invoiceId = $this->piModel->createInvoice($data, $items);
                if ($invoiceId) {
                    $this->setFlash('success', 'تم اعتماد الفاتورة وتسجيل المديونية والقيد المحاسبي بنجاح.');
                    $this->redirect('purchaseInvoice/show/' . $invoiceId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        $suppliers = $this->supplierModel ? $this->supplierModel->getAllSuppliers() : [];
        $products = [];
        if (file_exists('../app/models/Product.php')) {
            $prodModel = $this->model('Product');
            $products = $prodModel->getAllProducts();
        }

        $data = [
            'title' => 'تسجيل فاتورة مورد',
            'suppliers' => $suppliers,
            'products' => $products,
            'grn_data' => $grnData,
            'po_data' => $poData,
            'grn_items' => $grnItems,
            'auto_inv_num' => 'PI-' . date('Ymd') . '-' . rand(10,99),
            'breadcrumb' => [['label' => 'فواتير الموردين', 'url' => 'purchaseInvoice/index'], ['label' => 'جديد', 'url' => '#']]
        ];
        ob_start(); $this->view('purchaseInvoice/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchaseInvoice/index');
        $invId = (int)$id;
        
        $invoice = $this->piModel->getInvoiceById($invId);
        if (!$invoice) {
            $this->setFlash('error', 'الفاتورة غير موجودة.');
            $this->redirect('purchaseInvoice/index');
        }

        $items = $this->piModel->getInvoiceItems($invId);
        
        $data = [
            'title' => 'فاتورة مورد #' . $invoice->invoice_number,
            'invoice' => $invoice,
            'items' => $items,
            'breadcrumb' => [['label' => 'فواتير الموردين', 'url' => 'purchaseInvoice/index'], ['label' => 'التفاصيل', 'url' => '#']]
        ];
        
        ob_start(); $this->view('purchaseInvoice/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->piModel->deleteInvoice((int)$id);
            $this->setFlash('success', 'تم مسح الفاتورة (ملاحظة: تأكد من مراجعة القيود المحاسبية يدوياً).');
        }
        $this->redirect('purchaseInvoice/index');
    }
}