<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    
    private $productModel;

    public function __construct() {
        $this->requireAuth();
        $this->productModel = $this->model('Product');
    }

    public function index(): void {
        $products = $this->productModel->getAllProducts();
        
        $data = [
            'title' => 'المخزون ودليل الأصناف',
            'products' => $products,
            'breadcrumb' => [
                ['label' => 'المخازن', 'url' => '#'],
                ['label' => 'الأصناف', 'url' => 'product/index']
            ]
        ];
        
        ob_start();
        $this->view('products/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'category_id' => trim($_POST['category_id'] ?? ''),
                'sku' => trim($_POST['sku'] ?? ''),
                'barcode' => trim($_POST['barcode'] ?? ''),
                'unit' => trim($_POST['unit'] ?? 'قطعة'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => $_POST['price'] ?? 0,
                'cost' => $_POST['cost'] ?? 0,
                'quantity' => $_POST['quantity'] ?? 0,
                'reorder_point' => $_POST['reorder_point'] ?? 0,
                'track_batches' => isset($_POST['track_batches']) ? 1 : 0
            ];

            if (empty($data['name']) || empty($data['sku']) || empty($data['price'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإلزامية (الاسم، رمز التخزين، السعر).');
            } elseif ($this->productModel->skuExists($data['sku'])) {
                $this->setFlash('error', 'رمز التخزين SKU مسجل مسبقاً، يرجى اختيار رمز فريد.');
            } elseif ($this->productModel->createProduct($data)) {
                $this->setFlash('success', 'تم إضافة المنتج إلى المخزون بنجاح.');
                $this->redirect('product/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات المنتج.');
            }
        }

        $categories = $this->productModel->getCategories();
        $data = [
            'title' => 'إضافة صنف جديد',
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'الأصناف', 'url' => 'product/index'],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('products/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if (empty($id) || !is_numeric($id)) {
            $this->redirect('product/index');
        }

        $productId = (int)$id;
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->setFlash('error', 'المنتج غير موجود.');
            $this->redirect('product/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'category_id' => trim($_POST['category_id'] ?? ''),
                'sku' => trim($_POST['sku'] ?? ''),
                'barcode' => trim($_POST['barcode'] ?? ''),
                'unit' => trim($_POST['unit'] ?? 'قطعة'),
                'description' => trim($_POST['description'] ?? ''),
                'price' => $_POST['price'] ?? 0,
                'cost' => $_POST['cost'] ?? 0,
                'quantity' => $_POST['quantity'] ?? 0,
                'reorder_point' => $_POST['reorder_point'] ?? 0,
                'track_batches' => isset($_POST['track_batches']) ? 1 : 0
            ];

            if (empty($data['name']) || empty($data['sku']) || empty($data['price'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإلزامية.');
            } elseif ($this->productModel->skuExists($data['sku'], $productId)) {
                $this->setFlash('error', 'رمز الـ SKU مستخدم في منتج آخر.');
            } elseif ($this->productModel->updateProduct($productId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات المنتج بنجاح.');
                $this->redirect('product/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
            }
        }

        $categories = $this->productModel->getCategories();
        $data = [
            'title' => 'تعديل الصنف',
            'product' => $product,
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'الأصناف', 'url' => 'product/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('products/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->productModel->deleteProduct((int)$id)) {
                    $this->setFlash('success', 'تم حذف المنتج من قاعدة البيانات.');
                } else {
                    $this->setFlash('error', 'فشل الحذف. المنتج غير موجود.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف هذا المنتج لارتباطه بحركات مخزنية أو فواتير سابقة.');
            }
        }
        $this->redirect('product/index');
    }
}