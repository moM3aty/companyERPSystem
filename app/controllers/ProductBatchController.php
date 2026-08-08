<?php
// app/controllers/ProductBatchController.php

class ProductBatchController extends Controller {
    
    private $productBatchModel;

    public function __construct() {
        $this->requireAuth();
        $this->productBatchModel = $this->model('ProductBatch');
    }

    public function index(string $productId = '') {
        $batches = [];
        $product = null;
        $title = 'إدارة التشغيلات والسيريالات';

        if (!empty($productId) && is_numeric($productId)) {
            // إذا تم الدخول من شاشة المنتجات (لمنتج محدد)
            $prodId = (int)$productId;
            $productModel = $this->model('Product');
            $product = $productModel->findById($prodId);

            if (!$product) {
                $this->setFlash('error', 'المنتج غير موجود.');
                $this->redirect('product/index');
            }
            $batches = $this->productBatchModel->getBatchesByProduct($prodId);
            $title = 'التشغيلات والسيريالات: ' . $product->name;
        } else {
            // إذا تم الدخول من القائمة الجانبية (لكل المنتجات)
            $batches = $this->productBatchModel->getAllBatches();
        }

        $data = [
            'title' => $title,
            'product' => $product,
            'batches' => $batches,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'الأصناف', 'url' => 'product/index'],
                ['label' => 'التشغيلات', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('productBatch/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(string $productId = '') {
        if (empty($productId) || !is_numeric($productId)) $this->redirect('product/index');
        
        $prodId = (int)$productId;
        $productModel = $this->model('Product');
        $product = $productModel->findById($prodId);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'product_id' => $prodId,
                'lot_number' => trim($_POST['lot_number'] ?? ''),
                'batch_number' => trim($_POST['lot_number'] ?? ''), // حفظ نفس القيمة للحقل القديم
                'serial_number' => trim($_POST['serial_number'] ?? ''),
                'production_date' => trim($_POST['production_date'] ?? ''),
                'expiry_date' => trim($_POST['expiry_date'] ?? ''),
                'quantity' => (int)($_POST['quantity'] ?? 1),
                'notes' => trim($_POST['notes'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ];

            if (!$this->productBatchModel->isSerialNumberUnique($data['serial_number'])) {
                $this->setFlash('error', 'عفواً، رقم السيريال مستخدم مسبقاً في النظام.');
                $this->redirect('productBatch/create/' . $prodId);
                return;
            }

            if ($this->productBatchModel->addBatch($data)) {
                $this->setFlash('success', 'تم تسجيل التشغيلة بنجاح.');
                $this->redirect('productBatch/index/' . $prodId);
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ التشغيلة.');
            }
        }

        $data = [
            'title' => 'إضافة تشغيلة / سيريال جديد',
            'product' => $product,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'التشغيلات', 'url' => 'productBatch/index/' . $prodId],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('productBatch/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('product/index');
        
        $batchId = (int)$id;
        $batch = $this->productBatchModel->getBatchById($batchId);
        
        if (!$batch) {
            $this->setFlash('error', 'التشغيلة أو السيريال غير موجود.');
            $this->redirect('product/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'lot_number' => trim($_POST['lot_number'] ?? ''),
                'batch_number' => trim($_POST['lot_number'] ?? ''),
                'serial_number' => trim($_POST['serial_number'] ?? ''),
                'production_date' => trim($_POST['production_date'] ?? ''),
                'expiry_date' => trim($_POST['expiry_date'] ?? ''),
                'quantity' => (int)($_POST['quantity'] ?? 0),
                'notes' => trim($_POST['notes'] ?? ''),
                'status' => trim($_POST['status'] ?? 'active')
            ];

            if (!$this->productBatchModel->isSerialNumberUnique($data['serial_number'], $batchId)) {
                $this->setFlash('error', 'عفواً، رقم السيريال مستخدم مسبقاً لقطعة أخرى.');
                $this->redirect('productBatch/edit/' . $batchId);
                return;
            }

            if ($this->productBatchModel->updateBatch($batchId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات التشغيلة بنجاح.');
                $this->redirect('productBatch/index/' . $batch->product_id);
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
            }
        }

        $productModel = $this->model('Product');
        $product = $productModel->findById($batch->product_id);

        $data = [
            'title' => 'تعديل التشغيلة / السيريال',
            'batch' => $batch,
            'product' => $product,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'التشغيلات', 'url' => 'productBatch/index/' . $batch->product_id],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('productBatch/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete(string $id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $batchId = (int)$id;
            
            $batch = $this->productBatchModel->getBatchById($batchId);
            $productId = $batch ? $batch->product_id : '';

            if ($this->productBatchModel->deleteBatch($batchId)) {
                $this->setFlash('success', 'تم حذف التشغيلة/السيريال بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
            $this->redirect('productBatch/index/' . $productId);
        } else {
            $this->redirect('product/index');
        }
    }
}