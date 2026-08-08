<?php
// app/controllers/SalesOrderController.php

class SalesOrderController extends Controller {
    
    private $salesOrderModel;

    public function __construct() {
        $this->requireAuth();
        $this->salesOrderModel =$this->model('SalesOrder');
    }

    /* STREAMING_CHUNK: Index and Show... */
    public function index() {
        $orders = $this->salesOrderModel->getAllOrders();$data = [
            'title' => 'أوامر البيع (Sales Orders)',
            'orders' => $orders,
            'breadcrumb' => [
                ['label' => 'المبيعات', 'url' => '#'],
                ['label' => 'أوامر البيع', 'url' => 'salesOrder/index']
            ]
        ];
        
        ob_start();
        $this->view('salesOrder/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function show(string $id = '') {
        if (empty($id) || !is_numeric($id))$this->redirect('salesOrder/index');

        $orderId = (int)$id;
        $order = $this->salesOrderModel->getOrderById($orderId);
        
        if (!$order) {$this->setFlash('error', 'أمر البيع غير موجود أو تم حذفه.');
            $this->redirect('salesOrder/index');
        }

        $items = $this->salesOrderModel->getOrderItems($orderId);

        $data = [
            'title' => 'تفاصيل أمر البيع #' . $order->order_number,
            'order' => $order,
            'items' => $items,
            'breadcrumb' => [
                ['label' => 'أوامر البيع', 'url' => 'salesOrder/index'],
                ['label' => 'عرض الأمر', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('salesOrder/show', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    /* STREAMING_CHUNK: Create... */
    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'order_number' => trim($_POST['order_number'] ?? 'SO-' . time()),
                'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'customer_name' => trim($_POST['customer_name'] ?? 'عميل نقدي'),
                'order_date' => trim($_POST['order_date'] ?? date('Y-m-d')),
                'status' => trim($_POST['status'] ?? 'draft'),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            $productIds =$_POST['product_id'] ?? [];
            $quantities =$_POST['quantity'] ?? [];
            $prices =$_POST['price'] ?? [];
            
            $items = [];$totalAmount = 0;

            for ($i = 0; $i < count($productIds);$i++) {
                if (!empty($productIds[$i])) {$qty = (float)($quantities[$i] ?? 1);
                    $price = (float)($prices[$i] ?? 0);$subtotal = $qty * $price;
                    $totalAmount +=$subtotal;
                    
                    $items[] = [
                        'product_id' => (int)$productIds[$i],
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $data['total_amount'] =$totalAmount;

            if (empty($items)) {$this->setFlash('error', 'يجب إضافة صنف واحد على الأقل لأمر البيع.');
            } else {
                if ($this->salesOrderModel->createOrder($data, $items)) {$this->setFlash('success', 'تم إنشاء أمر البيع بنجاح.');
                    $this->redirect('salesOrder/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        // جلب المنتجات والعملاء لتعبئة القوائم
        $productModel = $this->model('Product');$products = $productModel->getAllProducts();$customerModel = $this->model('Customer');$customers = method_exists($customerModel, 'getAllCustomers') ?$customerModel->getAllCustomers() : [];

        $data = [
            'title' => 'إنشاء أمر بيع جديد',
            'products' => $products,
            'customers' => $customers,
            'default_order_number' => 'SO-' . date('ymd') . rand(10,99),
            'breadcrumb' => [
                ['label' => 'أوامر البيع', 'url' => 'salesOrder/index'],
                ['label' => 'جديد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('salesOrder/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    /* STREAMING_CHUNK: Edit and Delete... */
    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id))$this->redirect('salesOrder/index');

        $orderId = (int)$id;
        $order = $this->salesOrderModel->getOrderById($orderId);

        if (!$order) {$this->setFlash('error', 'أمر البيع غير موجود.');
            $this->redirect('salesOrder/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'customer_name' => trim($_POST['customer_name'] ?? 'عميل نقدي'),
                'order_date' => trim($_POST['order_date'] ?? date('Y-m-d')),
                'status' => trim($_POST['status'] ?? 'draft'),
                'notes' => trim($_POST['notes'] ?? '')
            ];

            $productIds =$_POST['product_id'] ?? [];
            $quantities =$_POST['quantity'] ?? [];
            $prices =$_POST['price'] ?? [];
            
            $items = [];$totalAmount = 0;

            for ($i = 0; $i < count($productIds);$i++) {
                if (!empty($productIds[$i])) {$qty = (float)($quantities[$i] ?? 1);
                    $price = (float)($prices[$i] ?? 0);$subtotal = $qty * $price;
                    $totalAmount +=$subtotal;
                    
                    $items[] = [
                        'product_id' => (int)$productIds[$i],
                        'quantity' => $qty,
                        'price' => $price,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $data['total_amount'] =$totalAmount;

            if (empty($items)) {$this->setFlash('error', 'يجب إضافة صنف واحد على الأقل.');
            } else {
                if ($this->salesOrderModel->updateOrder($orderId,$data, $items)) {$this->setFlash('success', 'تم تعديل بيانات وحالة أمر البيع بنجاح.');
                    $this->redirect('salesOrder/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                }
            }
        }

        $items =$this->salesOrderModel->getOrderItems($orderId);$productModel = $this->model('Product');$products = $productModel->getAllProducts();$customerModel = $this->model('Customer');$customers = method_exists($customerModel, 'getAllCustomers') ?$customerModel->getAllCustomers() : [];

        $data = [
            'title' => 'تعديل أمر البيع #' . $order->order_number,
            'order' => $order,
            'items' => $items,
            'products' => $products,
            'customers' => $customers,
            'breadcrumb' => [
                ['label' => 'أوامر البيع', 'url' => 'salesOrder/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('salesOrder/edit', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete(string $id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->salesOrderModel->deleteOrder((int)$id)) {$this->setFlash('success', 'تم حذف أمر البيع بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('salesOrder/index');
    }
}