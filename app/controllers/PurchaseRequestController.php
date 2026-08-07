<?php
// المسار: app/controllers/PurchaseRequestController.php

class PurchaseRequestController extends Controller {
    
    private PurchaseRequest $requestModel;

    public function __construct() {
        $this->requireAuth();
        $this->requestModel = $this->model('PurchaseRequest');
    }

    public function index(): void {
        $requests = $this->requestModel->getAllRequests();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'طلبات الشراء والاعتمادات (PR)',
            'requests' => $requests,
            'is_admin' => $isAdmin
        ];
        
        ob_start();
        $this->view('purchase_requests/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'request_date' => trim($_POST['request_date'] ?? date('Y-m-d')),
                'notes'        => trim($_POST['notes'] ?? '')
            ];

            $productIds = $_POST['product_id'] ?? [];
            $quantities = $_POST['quantity'] ?? [];
            $estimatedPrices = $_POST['estimated_price'] ?? [];

            if (empty($productIds)) {
                $this->setFlash('error', 'يجب إضافة صنف واحد على الأقل للطلب.');
                $this->redirect('purchaseRequest/create');
            }

            $items = [];
            foreach ($productIds as $index => $pid) {
                $qty = (int)($quantities[$index] ?? 0);
                $price = (float)($estimatedPrices[$index] ?? 0);
                
                if ($qty > 0) {
                    $items[] = [
                        'product_id' => (int)$pid,
                        'quantity' => $qty,
                        'estimated_price' => $price
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'الكميات المدخلة غير صالحة.');
                $this->redirect('purchaseRequest/create');
            }

            if ($this->requestModel->createPurchaseRequest($data, $items)) {
                $this->setFlash('success', 'تم إرسال طلب الشراء للإدارة وهو قيد المراجعة.');
                $this->redirect('purchaseRequest/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ طلب الشراء.');
                $this->redirect('purchaseRequest/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, price FROM products ORDER BY name ASC");
            $products = $db->resultSet();
            
            $data = [
                'title' => 'رفع طلب شراء داخلي',
                'products' => $products
            ];
            
            ob_start();
            $this->view('purchase_requests/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchaseRequest/index');

        $requestId = (int)$id;
        $request = $this->requestModel->getRequestById($requestId);
        
        if (!$request) {
            $this->setFlash('error', 'طلب الشراء غير موجود.');
            $this->redirect('purchaseRequest/index');
        }

        $items = $this->requestModel->getRequestItems($requestId);
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);

        $data = [
            'title' => 'تفاصيل واعتماد طلب الشراء',
            'request' => $request,
            'items' => $items,
            'is_admin' => $isAdmin
        ];

        ob_start();
        $this->view('purchase_requests/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function approve(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->requestModel->updateStatus((int)$id, 'approved', Session::getUserId())) {
                $this->setFlash('success', 'تم اعتماد طلب الشراء بنجاح! يمكنك الآن تحويله لأمر شراء للمورد.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الاعتماد.');
            }
        }
        $this->redirect('purchaseRequest/show/' . $id);
    }

    public function reject(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->requestModel->updateStatus((int)$id, 'rejected', Session::getUserId())) {
                $this->setFlash('success', 'تم رفض طلب الشراء.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الرفض.');
            }
        }
        $this->redirect('purchaseRequest/show/' . $id);
    }
}