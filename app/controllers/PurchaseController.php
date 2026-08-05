<?php
class PurchaseController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $db = Database::getInstance();
        $db->query('
            SELECT po.*, s.name as supplier_name
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.id
            ORDER BY po.id DESC
        ');
        $orders = $db->resultSet();
        
        $data = [
            'title' => 'أوامر الشراء',
            'orders' => $orders,
            'flash' => $this->getFlash()
        ];
        $this->view('purchase/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $supplierId = $_POST['supplier_id'];
            $notes = trim($_POST['notes'] ?? '');
            $items = [];
            
            // تجميع الأصناف من POST
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['unit_price'] ?? [];
            
            for ($i = 0; $i < count($productIds); $i++) {
                if (!empty($productIds[$i]) && $quantities[$i] > 0) {
                    $items[] = [
                        'product_id' => (int) $productIds[$i],
                        'quantity' => (int) $quantities[$i],
                        'unit_price' => (float) $prices[$i],
                    ];
                }
            }
            
            if (empty($items)) {
                $this->setFlash('error', 'يجب إضافة صنف واحد على الأقل');
                $this->redirect('purchase/create');
            }
            
            $purchaseModel = $this->model('Purchase');
            try {
                $poId = $purchaseModel->createPurchaseOrder($supplierId, $items, $notes);
                $this->setFlash('success', 'تم إنشاء أمر الشراء رقم ' . $poId . ' بنجاح');
            } catch (Exception $e) {
                $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
            }
            
            $this->redirect('purchase/index');
        } else {
            // جلب الموردين والمنتجات للاختيار
            $supplierModel = $this->model('Supplier');
            $productModel = $this->model('Product');
            $suppliers = $supplierModel->getSuppliers();
            $products = $productModel->getProducts();
            
            $data = [
                'title' => 'إنشاء أمر شراء',
                'suppliers' => $suppliers,
                'products' => $products,
                'flash' => $this->getFlash()
            ];
            $this->view('purchase/create', $data);
        }
    }

    // استلام البضاعة
    public function receive($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receivedItems = $_POST['received_items'] ?? [];
            $purchaseModel = $this->model('Purchase');
            try {
                $purchaseModel->receiveGoods($id, $receivedItems);
                $this->setFlash('success', 'تم استلام البضاعة بنجاح');
            } catch (Exception $e) {
                $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
            }
            $this->redirect('purchase/index');
        } else {
            // عرض تفاصيل الأمر وأصنافه لاستلامها
            $db = Database::getInstance();
            $db->query('
                SELECT po.*, s.name as supplier_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = :id
            ');
            $db->bind(':id', $id, PDO::PARAM_INT);
            $order = $db->single();
            
            if (!$order) {
                $this->setFlash('warning', 'أمر الشراء غير موجود');
                $this->redirect('purchase/index');
            }
            
            $db->query('SELECT * FROM purchase_order_items WHERE po_id = :id');
            $db->bind(':id', $id, PDO::PARAM_INT);
            $items = $db->resultSet();
            
            $data = [
                'title' => 'استلام بضاعة',
                'order' => $order,
                'items' => $items,
                'flash' => $this->getFlash()
            ];
            $this->view('purchase/receive', $data);
        }
    }
}