<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    
    /** @var Product كائن نموذج المنتجات */
    private Product $productModel;

    public function __construct() {
        // حماية المتحكم: يجب أن يكون المستخدم مسجل الدخول
        $this->requireAuth();
        
        // تحميل مودل المنتجات
        $this->productModel = $this->model('Product');
    }

    /**
     * عرض الصفحة الرئيسية للمنتجات (جدول المخزون)
     */
    public function index(): void {
        // جلب المنتجات مع تصنيفاتها من المودل
        $products = $this->productModel->getProductsWithCategory();
        
        $data = [
            'title'    => 'إدارة المخزون',
            'products' => $products,
            'flash'    => $this->getFlash()
        ];
        
        // استدعاء الـ View المطابق لما أرسلته سابقاً
        $this->view('products/index', $data);
    }

    /**
     * معالجة عرض نموذج الإضافة وحفظ البيانات في قاعدة البيانات
     */
    public function create(): void {
        if ($this->isPost()) {
            // تنظيف المدخلات القادمة من الـ Form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            // تجهيز مصفوفة البيانات للحفظ
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'sku'         => trim($_POST['sku'] ?? ''),
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'quantity'    => (int)($_POST['quantity'] ?? 0),
                'price'       => (float)($_POST['price'] ?? 0)
            ];

            // التحقق من الحقول المطلوبة
            if (empty($data['name']) || empty($data['sku'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإجبارية (اسم المنتج ورمز SKU).');
                $this->redirect('product/create');
            }

            // التحقق من أن رمز SKU غير مكرر
            if ($this->productModel->skuExists($data['sku'])) {
                $this->setFlash('error', 'رمز المنتج (SKU) موجود مسبقاً، يرجى اختيار رمز فريد.');
                $this->redirect('product/create');
            }

            // محاولة الحفظ في قاعدة البيانات باستخدام دالة create الموجودة في Model الأساسي
            if ($this->productModel->create($data)) {
                $this->setFlash('success', 'تم إضافة المنتج إلى المخزون بنجاح.');
                $this->redirect('product/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء الحفظ في قاعدة البيانات.');
                $this->redirect('product/create');
            }
        } else {
            // في حالة الطلب العادي (GET)، جلب التصنيفات لعرضها في قائمة الـ Select
            $categories = $this->productModel->getCategories();
            
            $data = [
                'title'      => 'إضافة منتج جديد',
                'categories' => $categories,
                'flash'      => $this->getFlash()
            ];
            
            $this->view('products/create', $data);
        }
    }

    /**
     * معالجة عرض نموذج التعديل وتحديث بيانات المنتج
     * 
     * @param string $id معرف المنتج (يتم تمريره من الرابط)
     */
    public function edit(string $id = ''): void {
        // التحقق من صحة المعرف
        if (empty($id) || !is_numeric($id)) {
            $this->setFlash('error', 'معرف المنتج غير صالح.');
            $this->redirect('product/index');
        }

        $productId = (int)$id;
        // جلب بيانات المنتج من قاعدة البيانات
        $product = $this->productModel->findById($productId);

        if (!$product) {
            $this->setFlash('error', 'المنتج المطلوب غير موجود.');
            $this->redirect('product/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'sku'         => trim($_POST['sku'] ?? ''),
                'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'quantity'    => (int)($_POST['quantity'] ?? 0),
                'price'       => (float)($_POST['price'] ?? 0)
            ];

            if (empty($data['name']) || empty($data['sku'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الإجبارية.');
                $this->redirect('product/edit/' . $productId);
            }

            // التحقق من التكرار مع استثناء المنتج الحالي
            if ($this->productModel->skuExists($data['sku'], $productId)) {
                $this->setFlash('error', 'رمز المنتج (SKU) مستخدم لمنتج آخر بالفعل.');
                $this->redirect('product/edit/' . $productId);
            }

            // التحديث في قاعدة البيانات
            if ($this->productModel->update($productId, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات المنتج بنجاح.');
                $this->redirect('product/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
                $this->redirect('product/edit/' . $productId);
            }
        } else {
            // جلب التصنيفات للـ Dropdown
            $categories = $this->productModel->getCategories();
            
            $data = [
                'title'      => 'تعديل منتج',
                'product'    => $product,
                'categories' => $categories,
                'flash'      => $this->getFlash()
            ];
            
            $this->view('products/edit', $data);
        }
    }

    /**
     * حذف منتج من قاعدة البيانات
     * 
     * @param string $id معرف المنتج المراد حذفه
     */
    public function delete(string $id = ''): void {
        // نطلب أن يكون الحذف عبر Post فقط لضمان الأمان
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $productId = (int)$id;
            
            // محاولة الحذف (قد تفشل إذا كان المنتج مرتبطاً بفاتورة سابقة بسبب القيود في قاعدة البيانات)
            try {
                if ($this->productModel->delete($productId)) {
                    $this->setFlash('success', 'تم حذف المنتج من المخزون بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ، لم يتم حذف المنتج.');
                }
            } catch (PDOException $e) {
                // التقاط الخطأ في حال ارتباط المنتج بجداول أخرى (مثل الفواتير)
                $this->setFlash('error', 'لا يمكن حذف هذا المنتج لأنه مرتبط بعمليات مالية (فواتير أو أوامر شراء سابقة).');
            }
        }
        
        $this->redirect('product/index');
    }
}