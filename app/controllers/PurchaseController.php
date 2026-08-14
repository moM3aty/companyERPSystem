<?php
// app/controllers/PurchaseController.php

class PurchaseController extends Controller {
    
    private $purchaseModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'accountant', 'purchasing'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->purchaseModel = $this->model('Purchase');
    }

    public function index() {
        $purchases = [];
        try {
            $purchases = $this->purchaseModel->getAllPurchases();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'أوامر الشراء (Purchase Orders)',
            'purchases' => is_array($purchases) ? $purchases : [],
            'breadcrumb' => [['label' => 'المشتريات', 'url' => '#'], ['label' => 'أوامر الشراء', 'url' => 'purchase/index']]
        ];
        
        ob_start(); $this->view('purchase/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'supplier_id'  => (int)($_POST['supplier_id'] ?? 0),
                'order_number' => trim($_POST['order_number'] ?? ''),
                'order_date'   => trim($_POST['order_date'] ?? date('Y-m-d')),
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
                        'product_name' => $productNames[$i] ?? 'صنف',
                        'quantity'     => (float)$quantities[$i],
                        'unit_price'   => (float)$prices[$i],
                        'total_price'  => (float)$totals[$i]
                    ];
                }
            }

            if (empty($data['supplier_id'])) {
                $this->setFlash('error', 'يجب اختيار المورد.');
                $this->redirect('purchase/create');
                return;
            }

            if (empty($items)) {
                $this->setFlash('error', 'يجب اختيار صنف واحد على الأقل من المخزون.');
                $this->redirect('purchase/create');
                return;
            }

            try {
                $purchaseId = $this->purchaseModel->createPurchase($data, $items);
                if ($purchaseId) {
                    $this->setFlash('success', 'تم إنشاء أمر الشراء (PO) بنجاح.');
                    $this->redirect('purchase/index');
                    return;
                }
            } catch (Throwable $e) {
                // سيظهر الخطأ التفصيلي إذا حدث
                $this->setFlash('error', 'تفاصيل الخطأ: ' . $e->getMessage());
            }
        }

        $db = Database::getInstance();
        $cid = Session::get('company_id') ?: 1;
        $suppliers = []; $products = [];
        
        try {
            $db->query("SELECT id, company_name as name FROM suppliers WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $suppliers = $db->resultSet() ?: [];
        } catch (Throwable $e) {}

        try {
            $db->query("SELECT id, name, cost_price as price FROM products WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $products = $db->resultSet() ?: [];
        } catch (Throwable $e) {
            try {
                $db->query("SELECT id, name, price FROM products WHERE company_id = :cid");
                $db->bind(':cid', $cid);
                $products = $db->resultSet() ?: [];
            } catch (Throwable $t) {}
        }

        $data = [
            'title' => 'إنشاء أمر شراء جديد (PO)',
            'suppliers' => $suppliers,
            'products' => $products,
            'auto_po_num' => 'PO-' . date('Ymd') . '-' . rand(100, 999)
        ];
        
        ob_start(); $this->view('purchase/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchase/index');
        
        $purchase = null;
        try { $purchase = $this->purchaseModel->getPurchaseById((int)$id); } catch (Throwable $e) {}
        
        if (!$purchase) {
            $this->setFlash('error', 'أمر الشراء غير موجود.');
            $this->redirect('purchase/index');
        }
        
        $items = $this->purchaseModel->getPurchaseItems($purchase->id);

        $data = [
            'title' => 'أمر شراء (PO) #' . $purchase->order_number,
            'purchase' => $purchase,
            'items' => $items,
            'breadcrumb' => [['label' => 'المشتريات', 'url' => 'purchase/index'], ['label' => 'عرض أمر الشراء', 'url' => '#']]
        ];
        
        ob_start(); $this->view('purchase/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->purchaseModel->deletePurchase((int)$id)) {
                $this->setFlash('success', 'تم حذف أمر الشراء بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
            }
        }
        $this->redirect('purchase/index');
    }
}