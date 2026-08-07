<?php
// المسار: app/controllers/PurchaseReturnController.php

class PurchaseReturnController extends Controller {
    
    private PurchaseReturn $returnModel;

    public function __construct() {
        $this->requireAuth();
        $this->returnModel = $this->model('PurchaseReturn');
    }

    public function index(): void {
        $returns = $this->returnModel->getAllReturns();
        
        $data = ['title' => 'مرتجعات المشتريات', 'returns' => $returns];
        
        ob_start();
        $this->view('purchase_returns/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
            $poId = !empty($_POST['po_id']) ? (int)$_POST['po_id'] : 0;
            $reason = trim($_POST['reason'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            if ($supplierId === 0 || empty($productIds)) {
                $this->setFlash('error', 'يجب تحديد المورد واختيار صنف واحد على الأقل.');
                $this->redirect('purchaseReturn/create');
            }

            $items = [];
            $totalRefund = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalRefund += $subtotal;
                    $items[] = ['product_id' => (int)$pid, 'quantity' => $qty, 'price' => $price, 'subtotal' => $subtotal];
                }
            }

            $returnData = ['supplier_id' => $supplierId, 'po_id' => $poId, 'total_refund' => $totalRefund, 'reason' => $reason];

            if ($this->returnModel->createReturn($returnData, $items)) {
                $this->setFlash('success', 'تم تسجيل المرتجع وتخفيض المخزون بنجاح.');
                $this->redirect('purchaseReturn/index');
            } else {
                $this->setFlash('error', 'فشل الحفظ. تأكد من توفر كمية كافية في المخزون.');
                $this->redirect('purchaseReturn/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM suppliers ORDER BY name ASC");
            $suppliers = $db->resultSet();
            $db->query("SELECT id, name, price, quantity FROM products WHERE quantity > 0 ORDER BY name ASC");
            $products = $db->resultSet();
            $db->query("SELECT id, po_number FROM purchase_orders ORDER BY created_at DESC LIMIT 50");
            $pos = $db->resultSet();

            $data = ['title' => 'تسجيل مرتجع مشتريات', 'suppliers' => $suppliers, 'products' => $products, 'pos' => $pos];
            ob_start();
            $this->view('purchase_returns/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}