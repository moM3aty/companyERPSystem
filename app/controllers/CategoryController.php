<?php
// app/controllers/CategoryController.php

class CategoryController extends Controller {
    
    private $categoryModel;

    public function __construct() {
        $this->requireAuth();
        $this->categoryModel =$this->model('Category');
    }

    /* STREAMING_CHUNK: Index and Create... */
    public function index(): void {
        $categories = $this->categoryModel->getAllCategories();$data = [
            'title' => 'تصنيفات المخزون',
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'المخازن', 'url' => '#'],
                ['label' => 'التصنيفات', 'url' => 'category/index']
            ]
        ];
        
        ob_start();
        $this->view('categories/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create(): void {
        if ($this->isPost()) {$data = [
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? ''))
            ];

            if (empty($data['name'])) {$this->setFlash('error', 'يرجى إدخال اسم التصنيف.');
            } else {
                try {
                    if ($this->categoryModel->createCategory($data)) {$this->setFlash('success', 'تم إضافة التصنيف بنجاح.');
                    } else {
                        $this->setFlash('error', 'حدث خطأ أثناء حفظ التصنيف.');
                    }
                } catch (Exception $e) {
                    $this->setFlash('error', 'مشكلة في قاعدة البيانات: ' . $e->getMessage());
                }
            }
        }
        $this->redirect('category/index');
    }

    /* STREAMING_CHUNK: Edit and Delete... */
    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id))$this->redirect('category/index');
        
        $catId = (int)$id;
        $category = $this->categoryModel->findById($catId);
        
        if (!$category) {$this->setFlash('error', 'التصنيف غير موجود.');
            $this->redirect('category/index');
        }

        if ($this->isPost()) {$data = [
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? ''))
            ];

            if (empty($data['name'])) {$this->setFlash('error', 'يرجى إدخال اسم التصنيف.');
            } else {
                try {
                    if ($this->categoryModel->update($catId, $data)) {$this->setFlash('success', 'تم تعديل التصنيف بنجاح.');
                        $this->redirect('category/index');
                        return;
                    } else {
                        $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                    }
                } catch (Exception $e) {
                    $this->setFlash('error', 'مشكلة في قاعدة البيانات: ' . $e->getMessage());
                }
            }
        }

        $data = [
            'title' => 'تعديل التصنيف',
            'category' => $category,
            'breadcrumb' => [
                ['label' => 'المخازن', 'url' => '#'],
                ['label' => 'التصنيفات', 'url' => 'category/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('categories/edit', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete(string $id = ''): void {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->categoryModel->deleteCategory((int)$id)) {$this->setFlash('success', 'تم حذف التصنيف بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
                }
            } catch (PDOException $e) {
                // منع الحذف إذا كان هناك منتجات داخل هذا التصنيف
                $this->setFlash('error', 'لا يمكن حذف هذا التصنيف لاحتوائه على منتجات مسجلة بالفعل.');
            }
        }
        $this->redirect('category/index');
    }
}