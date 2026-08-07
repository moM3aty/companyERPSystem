<?php
// المسار: app/controllers/ProductBatchController.php

class ProductBatchController extends Controller {
    private ProductBatch $batchModel;
    private Product $productModel;

    public function __construct() {
        $this->requireAuth();
        $this->batchModel = $this->model('ProductBatch');
        $this->productModel = $this->model('Product');
    }

    public function index(string $id = ''): void {
        $data = [
            'title' => 'إدارة التشغيلات والسيريال',
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => 'product/index'],
                ['label' => 'السيريال والتشغيلات', 'url' => '#']
            ]
        ];

        if (!empty($id) && is_numeric($id)) {
            // عرض تشغيلات منتج محدد
            $productId = (int)$id;
            $product = $this->productModel->findById($productId);
            if (!$product) $this->redirect('product/index');

            $batches = $this->batchModel->getBatchesByProduct($productId);
            $data['product'] = $product;
            $data['batches'] = $batches;
        } else {
            // العرض الشامل لكل التشغيلات من القائمة الجانبية
            $batches = $this->batchModel->getAllBatches();
            
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM products WHERE company_id = :cid AND track_batches = 1 ORDER BY name ASC");
            $db->bind(':cid', Session::get('company_id'));
            $products = $db->resultSet();
            
            $data['product'] = null;
            $data['batches'] = $batches;
            $data['products'] = $products;
        }
        
        ob_start();
        $this->view('products/batches', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(string $productId = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        if ($this->isPost()) {
            // تحديد المنتج إما من الرابط أو من الـ Select
            $pid = !empty($productId) ? (int)$productId : (int)($_POST['product_id'] ?? 0);
            
            if ($pid === 0) {
                $this->setFlash('error', 'يجب تحديد المنتج لتسجيل التشغيلة.');
                $this->redirect('productBatch/index');
            }

            $serialNumber = trim($_POST['serial_number'] ?? '');
            
            if (!empty($serialNumber) && !$this->batchModel->isSerialNumberUnique($serialNumber)) {
                $this->setFlash('error', 'خطأ: السيريال نمبر مسجل مسبقاً لقطعة أخرى.');
                $this->redirect('productBatch/index/' . ($productId ? $productId : ''));
            }

            $data = [
                'product_id' => $pid,
                'lot_number' => trim($_POST['lot_number'] ?? ''),
                'serial_number' => $serialNumber,
                'production_date' => !empty($_POST['production_date']) ? $_POST['production_date'] : null,
                'expiry_date' => !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null,
                'quantity' => (int)($_POST['quantity'] ?? 1),
                'status' => trim($_POST['status'] ?? 'available')
            ];

            if ($this->batchModel->addBatch($data)) {
                $this->setFlash('success', 'تم تسجيل التشغيلة / السيريال بنجاح في قاعدة البيانات.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإضافة.');
            }
        }
        $this->redirect('productBatch/index/' . ($productId ? $productId : ''));
    }
}