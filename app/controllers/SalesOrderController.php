<?php
// المسار: app/controllers/SalesOrderController.php

class SalesOrderController extends Controller {
    
    private SalesOrder $orderModel;

    public function __construct() {
        $this->requireAuth();
        $this->orderModel = $this->model('SalesOrder');
    }

    public function index(): void {
        $orders = $this->orderModel->getAllOrders();
        $data = ['title' => 'أوامر البيع (SO)', 'orders' => $orders];
        
        ob_start();
        $this->view('sales_orders/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
            $orderDate = trim($_POST['order_date'] ?? date('Y-m-d'));
            $notes = trim($_POST['notes'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            if ($customerId === 0 || empty($productIds)) {
                $this->setFlash('error', 'يجب اختيار عميل وإضافة صنف واحد على الأقل.');
                $this->redirect('salesOrder/create');
            }

            $items = [];
            $totalAmount = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $items[] = ['product_id' => (int)$pid, 'quantity' => $qty, 'price' => $price, 'subtotal' => $subtotal];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'بيانات الأصناف غير صالحة.');
                $this->redirect('salesOrder/create');
            }

            $orderData = ['customer_id' => $customerId, 'order_date' => $orderDate, 'status' => 'draft', 'total_amount' => $totalAmount, 'notes' => $notes];

            if ($this->orderModel->createOrder($orderData, $items)) {
                $this->setFlash('success', 'تم حفظ أمر البيع بنجاح.');
                $this->redirect('salesOrder/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                $this->redirect('salesOrder/create');
            }

        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name, price FROM products ORDER BY name ASC");
            $products = $db->resultSet();

            $data = ['title' => 'إنشاء أمر بيع', 'customers' => $customers, 'products' => $products];
            ob_start();
            $this->view('sales_orders/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    // --- وظيفة التعديل الجديدة (Edit) ---
    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('salesOrder/index');
        
        $orderId = (int)$id;
        $order = $this->orderModel->getOrderById($orderId);

        if (!$order || $order->status !== 'draft') {
            $this->setFlash('error', 'لا يمكن تعديل أمر البيع لأنه معتمد أو ملغي.');
            $this->redirect('salesOrder/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $customerId = !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
            $orderDate = trim($_POST['order_date'] ?? date('Y-m-d'));
            $notes = trim($_POST['notes'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['price'] ?? [];

            $items = [];
            $totalAmount = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $items[] = ['product_id' => (int)$pid, 'quantity' => $qty, 'price' => $price, 'subtotal' => $subtotal];
                }
            }

            $orderData = ['customer_id' => $customerId, 'order_date' => $orderDate, 'total_amount' => $totalAmount, 'notes' => $notes];

            if ($this->orderModel->updateOrder($orderId, $orderData, $items)) {
                $this->setFlash('success', 'تم تعديل أمر البيع بنجاح.');
                $this->redirect('salesOrder/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('salesOrder/edit/' . $orderId);
            }
        } else {
            $items = $this->orderModel->getOrderItems($orderId);
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name, price FROM products ORDER BY name ASC");
            $products = $db->resultSet();

            $data = ['title' => 'تعديل أمر البيع', 'order' => $order, 'items' => $items, 'customers' => $customers, 'products' => $products];
            ob_start();
            $this->view('sales_orders/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->orderModel->deleteOrder((int)$id)) {
                $this->setFlash('success', 'تم حذف أمر البيع.');
            } else {
                $this->setFlash('error', 'لا يمكن حذف أمر بيع غير مسودة.');
            }
        }
        $this->redirect('salesOrder/index');
    }
}