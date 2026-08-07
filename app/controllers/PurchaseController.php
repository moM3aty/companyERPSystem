<?php
// app/controllers/PurchaseController.php

class PurchaseController extends Controller {
    
    private Purchase $purchaseModel;

    public function __construct() {
        $this->requireAuth();
        $this->purchaseModel = $this->model('Purchase');
    }

    public function index(): void {
        $orders = $this->purchaseModel->getAllOrders();
        
        $data = [
            'title'  => 'أوامر الشراء (PO)',
            'orders' => $orders
        ];
        
        ob_start();
        $this->view('purchase/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
            $notes = trim($_POST['notes'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['unit_price'] ?? [];

            if ($supplierId === 0 || empty($productIds)) {
                $this->setFlash('error', 'يجب اختيار مورد وإضافة منتج واحد على الأقل.');
                $this->redirect('purchase/create');
            }

            $items = [];
            $totalAmount = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $items[] = [
                        'product_id'       => (int)$pid,
                        'quantity_ordered' => $qty,
                        'unit_price'       => $price,
                        'total'            => $subtotal
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'الكميات غير صالحة.');
                $this->redirect('purchase/create');
            }

            $orderData = ['supplier_id' => $supplierId, 'total_amount' => $totalAmount, 'notes' => $notes, 'status' => 'pending'];

            if ($this->purchaseModel->createOrder($orderData, $items)) {
                $this->setFlash('success', 'تم إنشاء أمر الشراء بنجاح. يمكنك الآن استلام البضائع.');
                $this->redirect('purchase/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ أمر الشراء.');
                $this->redirect('purchase/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM suppliers ORDER BY name ASC");
            $suppliers = $db->resultSet();
            $db->query("SELECT id, name, price FROM products ORDER BY name ASC");
            $products = $db->resultSet();

            $data = ['title' => 'إنشاء أمر شراء', 'suppliers' => $suppliers, 'products' => $products];
            ob_start();
            $this->view('purchase/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        // ... نفس كود الدفعة السابقة (التعديل متاح للمسودات فقط)
        if (empty($id) || !is_numeric($id)) $this->redirect('purchase/index');
        
        $poId = (int)$id;
        $order = $this->purchaseModel->getOrderById($poId);

        if (!$order || $order->status !== 'pending') {
            $this->setFlash('error', 'لا يمكن تعديل أمر الشراء لأنه معتمد أو مستلم.');
            $this->redirect('purchase/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
            $notes = trim($_POST['notes'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['unit_price'] ?? [];

            $items = [];
            $totalAmount = 0.0;

            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($prices[$index] ?? 0);
                if ($qty > 0 && $price >= 0) {
                    $subtotal = $qty * $price;
                    $totalAmount += $subtotal;
                    $items[] = [
                        'product_id' => (int)$pid, 'quantity_ordered' => $qty, 'unit_price' => $price, 'total' => $subtotal
                    ];
                }
            }

            $orderData = ['supplier_id' => $supplierId, 'total_amount' => $totalAmount, 'notes' => $notes];

            if ($this->purchaseModel->updateOrder($poId, $orderData, $items)) {
                $this->setFlash('success', 'تم تعديل أمر الشراء بنجاح.');
                $this->redirect('purchase/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء تعديل أمر الشراء.');
                $this->redirect('purchase/edit/' . $poId);
            }
        } else {
            $items = $this->purchaseModel->getOrderItems($poId);
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM suppliers ORDER BY name ASC");
            $suppliers = $db->resultSet();
            $db->query("SELECT id, name, price FROM products ORDER BY name ASC");
            $products = $db->resultSet();

            $data = ['title' => 'تعديل أمر الشراء', 'order' => $order, 'items' => $items, 'suppliers' => $suppliers, 'products' => $products];
            ob_start();
            $this->view('purchase/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchase/index');
        
        $poId = (int)$id;
        $order = $this->purchaseModel->getOrderById($poId);
        if (!$order) $this->redirect('purchase/index');
        
        $items = $this->purchaseModel->getOrderItems($poId);
        $data = ['title' => 'تفاصيل أمر الشراء', 'order' => $order, 'items' => $items];
        
        ob_start();
        $this->view('purchase/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function receive(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchase/index');
        $poId = (int)$id;
        $order = $this->purchaseModel->getOrderById($poId);

        if (!$order || !in_array($order->status, ['pending', 'approved', 'ordered'])) {
            $this->setFlash('error', 'لا يمكن استلام بضائع لهذا الطلب.');
            $this->redirect('purchase/index');
        }

        if ($this->isPost()) {
            $receivedItems = $_POST['received_items'] ?? [];
            // الموديل سيقوم بـ: رفع المخزون + حساب تكلفة المستلم + إنشاء قيد يومية من حـ/المخزون إلى حـ/الموردين
            if ($this->purchaseModel->receiveItems($poId, $receivedItems)) {
                $this->setFlash('success', 'تم استلام الكميات بنجاح. تم تحديث المخزون وإثبات قيد محاسبي باستحقاق المورد.');
                $this->redirect('purchase/show/' . $poId);
            } else {
                $this->setFlash('error', 'فشل في استلام البضائع أو توليد القيد المحاسبي.');
                $this->redirect('purchase/receive/' . $poId);
            }
        } else {
            $items = $this->purchaseModel->getOrderItems($poId);
            $data = ['title' => 'استلام بضاعة', 'order' => $order, 'items' => $items];
            ob_start();
            $this->view('purchase/receive', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->purchaseModel->deleteOrder((int)$id)) {
                $this->setFlash('success', 'تم حذف أمر الشراء.');
            } else {
                $this->setFlash('error', 'فشل الحذف. قد يكون الأمر معتمداً.');
            }
        }
        $this->redirect('purchase/index');
    }
}