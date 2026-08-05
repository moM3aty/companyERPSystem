<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول تلقائياً
        $this->requireAuth();
    }

    // عرض قائمة المنتجات
    public function index() {
        $productModel = $this->model('Product');
        $data = [
            'title' => 'إدارة المخزون',
            'products' => $productModel->getProducts(),
            'flash' => $this->getFlash()
        ];
        $this->view('products/index', $data);
    }

    // إضافة منتج جديد
    public function create() {
        $productModel = $this->model('Product');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'sku' => trim($_POST['sku']),
                'category_id' => trim($_POST['category_id']),
                'quantity' => trim($_POST['quantity']),
                'price' => trim($_POST['price'])
            ];

            // التحقق من البيانات
            $errors = [];
            if (empty($data['name'])) $errors[] = 'اسم المنتج مطلوب';
            if (empty($data['sku'])) $errors[] = 'رمز المنتج (SKU) مطلوب';
            if (empty($data['quantity']) || $data['quantity'] < 0) $errors[] = 'الكمية يجب أن تكون 0 أو أكثر';
            if (empty($data['price']) || $data['price'] <= 0) $errors[] = 'السعر يجب أن يكون أكبر من صفر';

            if (empty($errors)) {
                if ($productModel->addProduct($data)) {
                    $this->setFlash('success', 'تم إضافة المنتج "' . $data['name'] . '" بنجاح');
                    $this->redirect('product/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء إضافة المنتج (ربما يكون الـ SKU مكرراً)');
                    $this->redirect('product/create');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
                $this->redirect('product/create');
            }
            exit();
        } else {
            $data = [
                'title' => 'إضافة منتج جديد',
                'categories' => $productModel->getCategories(),
                'flash' => $this->getFlash()
            ];
            $this->view('products/create', $data);
        }
    }

    // تعديل بيانات منتج
    public function edit($id) {
        $productModel = $this->model('Product');
        $product = $productModel->getProductById($id);
        
        if (!$product) {
            $this->setFlash('warning', 'المنتج غير موجود');
            $this->redirect('product/index');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'sku' => trim($_POST['sku']),
                'category_id' => trim($_POST['category_id']),
                'quantity' => trim($_POST['quantity']),
                'price' => trim($_POST['price'])
            ];

            // التحقق من البيانات
            $errors = [];
            if (empty($data['name'])) $errors[] = 'اسم المنتج مطلوب';
            if (empty($data['sku'])) $errors[] = 'رمز المنتج (SKU) مطلوب';
            if (empty($data['quantity']) || $data['quantity'] < 0) $errors[] = 'الكمية يجب أن تكون 0 أو أكثر';
            if (empty($data['price']) || $data['price'] <= 0) $errors[] = 'السعر يجب أن يكون أكبر من صفر';

            if (empty($errors)) {
                if ($productModel->updateProduct($data, $id)) {
                    $this->setFlash('success', 'تم تحديث بيانات المنتج "' . $data['name'] . '" بنجاح');
                    $this->redirect('product/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تعديل المنتج');
                    $this->redirect('product/edit/' . $id);
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
                $this->redirect('product/edit/' . $id);
            }
            exit();
        } else {
            $data = [
                'title' => 'تعديل بيانات منتج',
                'product' => $product,
                'categories' => $productModel->getCategories(),
                'flash' => $this->getFlash()
            ];
            $this->view('products/edit', $data);
        }
    }

    // حذف منتج
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('product/index');
        }

        $productModel = $this->model('Product');
        $product = $productModel->getProductById($id);
        
        if (!$product) {
            $this->setFlash('warning', 'المنتج غير موجود');
            $this->redirect('product/index');
        }

        if ($productModel->deleteProduct($id)) {
            $this->setFlash('success', 'تم حذف المنتج "' . $product->name . '" بنجاح');
        } else {
            $this->setFlash('error', 'حدث خطأ أثناء الحذف');
        }
        
        $this->redirect('product/index');
    }
}