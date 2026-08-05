<?php
// app/controllers/PurchaseController.php

class PurchaseController extends Controller {
    
    /** @var Purchase */
    private Purchase $purchaseModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->purchaseModel = $this->model('Purchase');
    }

    /**
     * عرض قائمة جميع أوامر الشراء
     */
    public function index(): void {
        $orders = $this->purchaseModel->getAllOrders();
        
        $data = [
            'title'  => 'أوامر الشراء',
            'orders' => $orders,
            'flash'  => $this->getFlash()
        ];
        
        $this->view('purchase/index', $data);
    }

    /**
     * إنشاء أمر شراء جديد
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $supplierId = !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : 0;
            $notes = trim($_POST['notes'] ?? '');
            
            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $prices = $_POST['unit_price'] ?? [];

            // التحقق من صحة البيانات
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

            $orderData = [
                'supplier_id'  => $supplierId,
                'total_amount' => $totalAmount,
                'notes'        => $notes,
                'status'       => 'pending'
            ];

            // حفظ الطلب في قاعدة البيانات
            if ($this->purchaseModel->createOrder($orderData, $items)) {
                $this->setFlash('success', 'تم إنشاء أمر الشراء بنجاح وهو الآن قيد الانتظار.');
                $this->redirect('purchase/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ أمر الشراء.');
                $this->redirect('purchase/create');
            }
        } else {
            // جلب البيانات اللازمة للنموذج (الموردين والمنتجات)
            $supplierModel = $this->model('Supplier');
            $productModel = $this->model('Product');
            
            // استخدام دوال getAll الموجودة في Model الأساسي
            $suppliers = $supplierModel->getAll('name', 'ASC');
            $products = $productModel->getAll('name', 'ASC');

            $data = [
                'title'     => 'إنشاء أمر شراء',
                'suppliers' => $suppliers,
                'products'  => $products,
                'flash'     => $this->getFlash()
            ];
            
            $this->view('purchase/create', $data);
        }
    }

    /**
     * عرض تفاصيل أمر الشراء
     */
    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('purchase/index');
        }

        $poId = (int)$id;
        $order = $this->purchaseModel->getOrderById($poId);
        
        if (!$order) {
            $this->setFlash('error', 'أمر الشراء غير موجود.');
            $this->redirect('purchase/index');
        }

        $items = $this->purchaseModel->getOrderItems($poId);

        $data = [
            'title' => 'تفاصيل أمر الشراء',
            'order' => $order,
            'items' => $items,
            'flash' => $this->getFlash()
        ];

        $this->view('purchase/view', $data);
    }

    /**
     * استلام البضائع وتحديث المخزون ورصيد المورد
     */
    public function receive(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('purchase/index');
        }

        $poId = (int)$id;
        $order = $this->purchaseModel->getOrderById($poId);

        if (!$order || !in_array($order->status, ['pending', 'approved', 'ordered'])) {
            $this->setFlash('error', 'لا يمكن استلام بضائع لهذا الطلب (قد يكون مستلماً بالكامل أو ملغياً).');
            $this->redirect('purchase/index');
        }

        if ($this->isPost()) {
            // المصفوفة القادمة من الـ Form ستكون بهذا الشكل:
            // received_items[product_id][quantity_received]
            $receivedItems = $_POST['received_items'] ?? [];

            if (empty($receivedItems)) {
                $this->setFlash('error', 'لم يتم إرسال أي كميات للاستلام.');
                $this->redirect('purchase/receive/' . $poId);
            }

            // تنفيذ عملية الاستلام المعقدة عبر المودل
            if ($this->purchaseModel->receiveItems($poId, $receivedItems)) {
                $this->setFlash('success', 'تم استلام الكميات بنجاح، وتم تحديث المخزون وحساب المورد.');
                $this->redirect('purchase/show/' . $poId);
            } else {
                $this->setFlash('error', 'فشل في استلام البضائع أو تحديث المخزون.');
                $this->redirect('purchase/receive/' . $poId);
            }
        } else {
            $items = $this->purchaseModel->getOrderItems($poId);

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