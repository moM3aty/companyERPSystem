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
        if (empty($id) || !is_numeric($id)) $this->redirect('product/index');
        $productId = (int)$id;
        
        $product = $this->productModel->findById($productId);
        if (!$product) $this->redirect('product/index');

        $batches = $this->batchModel->getBatchesByProduct($productId);

        $data = [
            'title' => 'إدارة التشغيلات والسيريال',
            'product' => $product,
            'batches' => $batches,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => 'product/index'],
                ['label' => 'السيريال نمبر', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('products/batches', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(string $productId = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        if ($this->isPost() && !empty($productId) && is_numeric($productId)) {
            $serialNumber = trim($_POST['serial_number'] ?? '');
            
            if (!empty($serialNumber) && !$this->batchModel->isSerialNumberUnique($serialNumber)) {
                $this->setFlash('error', 'خطأ: السيريال نمبر مسجل مسبقاً لقطعة أخرى.');
                $this->redirect('productBatch/index/' . $productId);
            }

            $data = [
                'product_id' => (int)$productId,
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
        $this->redirect('productBatch/index/' . $productId);
    }
}