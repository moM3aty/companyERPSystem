<?php
// المسار: app/controllers/ProductController.php

class ProductController extends Controller {
    
    private Product $productModel;

    public function __construct() {
        $this->requireAuth();
        $this->productModel = $this->model('Product');
    }

    public function index(): void {
        $products = $this->productModel->getProductsWithCategory();
        $data = [
            'title' => 'دليل المخزون والأصناف',
            'products' => $products
        ];
        
        ob_start();
        $this->view('products/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'unit'          => trim($_POST['unit'] ?? 'قطعة'),
                'sku'           => trim($_POST['sku'] ?? ''),
                'barcode'       => trim($_POST['barcode'] ?? ''),
                'category_id'   => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'quantity'      => (int)($_POST['quantity'] ?? 0),
                'reorder_point' => (int)($_POST['reorder_point'] ?? 5),
                'track_batches' => isset($_POST['track_batches']) ? 1 : 0,
                'price'         => (float)($_POST['price'] ?? 0.0)
            ];

            if (empty($data['name']) || empty($data['sku'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإجبارية (الاسم، رمز SKU).');
                $this->redirect('product/create');
            }

            if ($this->productModel->skuExists($data['sku'])) {
                $this->setFlash('error', 'رمز الـ SKU مسجل مسبقاً.');
                $this->redirect('product/create');
            }

            if ($this->productModel->createProduct($data)) {
                $this->setFlash('success', 'تم إضافة الصنف للمخزون بنجاح.');
                $this->redirect('product/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                $this->redirect('product/create');
            }
        } else {
            $categories = $this->productModel->getCategories();
            $data = ['title' => 'إضافة صنف جديد', 'categories' => $categories];
            
            ob_start();
            $this->view('products/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    // --- وظيفة التعديل الجديدة (Edit) ---
    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('product/index');
        
        $productId = (int)$id;
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->setFlash('error', 'لم يتم العثور على الصنف.');
            $this->redirect('product/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'unit'          => trim($_POST['unit'] ?? 'قطعة'),
                'sku'           => trim($_POST['sku'] ?? ''),
                'barcode'       => trim($_POST['barcode'] ?? ''),
                'category_id'   => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'quantity'      => (int)($_POST['quantity'] ?? 0),
                'reorder_point' => (int)($_POST['reorder_point'] ?? 5),
                'track_batches' => isset($_POST['track_batches']) ? 1 : 0,
                'price'         => (float)($_POST['price'] ?? 0.0)
            ];

            if (empty($data['name']) || empty($data['sku'])) {
                $this->setFlash('error', 'الاسم ورمز SKU مطلوبان.');
                $this->redirect('product/edit/' . $productId);
            }

            if ($this->productModel->skuExists($data['sku'], $productId)) {
                $this->setFlash('error', 'رمز الـ SKU مستخدم في منتج آخر.');
                $this->redirect('product/edit/' . $productId);
            }

            if ($this->productModel->updateProduct($productId, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات الصنف بنجاح.');
                $this->redirect('product/index');
            } else {
                $this->setFlash('error', 'فشل في تحديث بيانات الصنف.');
                $this->redirect('product/edit/' . $productId);
            }
        } else {
            $categories = $this->productModel->getCategories();
            $data = ['title' => 'تعديل بيانات الصنف', 'product' => $product, 'categories' => $categories];
            
            ob_start();
            $this->view('products/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->productModel->delete((int)$id)) {
                    $this->setFlash('success', 'تم حذف الصنف بنجاح.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف هذا الصنف لارتباطه بحركات أو فواتير مسجلة.');
            }
        }
        $this->redirect('product/index');
    }
}