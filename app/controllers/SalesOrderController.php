<?php
// app/controllers/SalesOrderController.php

class SalesOrderController extends Controller {
    
    private $salesOrderModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'sales', 'accountant'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->salesOrderModel = $this->model('SalesOrder');
    }

    public function index() {
        $orders = [];
        try {
            $orders = $this->salesOrderModel->getAllSalesOrders();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'أوامر البيع (Sales Orders)',
            'orders' => is_array($orders) ? $orders : [],
            'breadcrumb' => [['label' => 'المبيعات', 'url' => '#'], ['label' => 'أوامر البيع', 'url' => 'salesOrder/index']]
        ];
        
        ob_start(); $this->view('sales_order/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'order_number'      => trim($_POST['order_number'] ?? ''),
                'customer_id'       => (int)($_POST['customer_id'] ?? 0),
                'order_date'        => trim($_POST['order_date'] ?? date('Y-m-d')),
                'expected_delivery' => trim($_POST['expected_delivery'] ?? ''),
                'total_amount'      => (float)($_POST['grand_total'] ?? 0),
                'notes'             => trim($_POST['notes'] ?? '')
            ];

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
                        'product_name' => $productNames[$i] ?? 'منتج',
                        'quantity'     => (float)$quantities[$i],
                        'unit_price'   => (float)$prices[$i],
                        'total_price'  => (float)$totals[$i]
                    ];
                }
            }

            if (empty($data['customer_id'])) {
                $this->setFlash('error', 'يجب اختيار العميل.');
                $this->redirect('salesOrder/create');
                return;
            }

            if (empty($items)) {
                $this->setFlash('error', 'يجب اختيار صنف واحد على الأقل من المخزون.');
                $this->redirect('salesOrder/create');
                return;
            }

            try {
                $orderId = $this->salesOrderModel->createSalesOrder($data, $items);
                if ($orderId) {
                    $this->setFlash('success', 'تم إنشاء أمر البيع بنجاح.');
                    $this->redirect('salesOrder/show/' . $orderId);
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', 'تفاصيل الخطأ التقني: ' . $e->getMessage());
            }
        }

        $db = Database::getInstance();
        $cid = Session::get('company_id') ?: 1;
        $customers = []; $products = [];
        
        try {
            $db->query("SELECT id, name FROM customers WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $customers = $db->resultSet() ?: [];
        } catch (Throwable $e) {}

        try {
            $db->query("SELECT id, name, sell_price as price FROM products WHERE company_id = :cid");
            $db->bind(':cid', $cid);
            $products = $db->resultSet() ?: [];
        } catch (Throwable $e) {
            try {
                $db->query("SELECT id, name, price FROM products WHERE company_id = :cid");
                $db->bind(':cid', $cid);
                $products = $db->resultSet() ?: [];
            } catch(Throwable $t) {}
        }

        $data = [
            'title' => 'إنشاء أمر بيع جديد',
            'customers' => $customers,
            'products' => $products,
            'auto_order_num' => 'SO-' . date('Ymd') . '-' . rand(100, 999)
        ];
        
        ob_start(); $this->view('sales_order/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('salesOrder/index');
        
        $order = null;
        try { $order = $this->salesOrderModel->getSalesOrderById((int)$id); } catch (Throwable $e) {}
        
        if (!$order) {
            $this->setFlash('error', 'أمر البيع غير موجود.');
            $this->redirect('salesOrder/index');
        }
        
        $items = $this->salesOrderModel->getSalesOrderItems($order->id);

        $data = [
            'title' => 'أمر بيع #' . $order->order_number,
            'order' => $order,
            'items' => $items,
            'breadcrumb' => [['label' => 'المبيعات', 'url' => 'salesOrder/index'], ['label' => 'عرض أمر البيع', 'url' => '#']]
        ];
        
        ob_start(); $this->view('sales_order/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin', 'manager']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->salesOrderModel->deleteSalesOrder((int)$id)) {
                $this->setFlash('success', 'تم حذف أمر البيع بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
            }
        }
        $this->redirect('salesOrder/index');
    }
}