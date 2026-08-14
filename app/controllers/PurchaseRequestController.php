<?php
// app/controllers/PurchaseRequestController.php

class PurchaseRequestController extends Controller {
    
    private $requestModel;

    public function __construct() {
        $this->requireAuth();
        $this->requestModel = $this->model('PurchaseRequest');
    }

    public function index() {
        $requests = [];
        try {
            $requests = $this->requestModel->getAllRequests();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'طلبات الاحتياج (Purchase Requests)',
            'requests' => is_array($requests) ? $requests : [],
            'breadcrumb' => [['label' => 'المشتريات', 'url' => '#'], ['label' => 'طلبات الشراء', 'url' => 'purchaseRequest/index']]
        ];
        
        ob_start(); $this->view('purchase_request/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'request_number'  => trim($_POST['request_number'] ?? ''),
                'department'      => trim($_POST['department'] ?? ''),
                'request_date'    => trim($_POST['request_date'] ?? date('Y-m-d')),
                'total_estimated' => (float)($_POST['grand_total'] ?? 0),
                'notes'           => trim($_POST['notes'] ?? '')
            ];

            $productNames = $_POST['product_name'] ?? [];
            $quantities   = $_POST['quantity'] ?? [];
            $prices       = $_POST['estimated_price'] ?? [];
            $totals       = $_POST['total_price'] ?? [];

            $items = [];
            for ($i = 0; $i < count($productNames); $i++) {
                if (!empty($productNames[$i]) && $quantities[$i] > 0) {
                    $items[] = [
                        'product_name'    => $productNames[$i],
                        'quantity'        => (float)$quantities[$i],
                        'estimated_price' => (float)$prices[$i],
                        'total_price'     => (float)$totals[$i]
                    ];
                }
            }

            if (empty($items)) {
                $this->setFlash('error', 'يجب إدخال صنف واحد على الأقل في الطلب.');
                $this->redirect('purchaseRequest/create');
                return;
            }

            try {
                $reqId = $this->requestModel->createRequest($data, $items);
                if ($reqId) {
                    $this->setFlash('success', 'تم رفع طلب الشراء الداخلي بنجاح وبانتظار الاعتماد.');
                    $this->redirect('purchaseRequest/show/' . $reqId);
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', 'تفاصيل الخطأ: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => 'إنشاء طلب شراء داخلي (PR)',
            'auto_req_num' => 'PR-' . date('Ymd') . '-' . rand(10, 99)
        ];
        
        ob_start(); $this->view('purchase_request/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('purchaseRequest/index');
        
        $req = null;
        try { $req = $this->requestModel->getRequestById((int)$id); } catch (Throwable $e) {}
        
        if (!$req) {
            $this->setFlash('error', 'الطلب غير موجود.');
            $this->redirect('purchaseRequest/index');
        }
        
        $items = $this->requestModel->getRequestItems($req->id);

        $data = [
            'title' => 'طلب احتياج #' . $req->request_number,
            'request' => $req,
            'items' => $items,
            'breadcrumb' => [['label' => 'الطلبات', 'url' => 'purchaseRequest/index'], ['label' => 'عرض الطلب', 'url' => '#']]
        ];
        
        ob_start(); $this->view('purchase_request/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']); 
        if ($this->isPost() && !empty($id)) {
            if ($this->requestModel->deleteRequest((int)$id)) {
                $this->setFlash('success', 'تم مسح طلب الشراء بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء المسح.');
            }
        }
        $this->redirect('purchaseRequest/index');
    }
}