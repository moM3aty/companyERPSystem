<?php
// المسار: app/controllers/CategoryController.php

class CategoryController extends Controller {
    
    private Category $categoryModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        $this->categoryModel = $this->model('Category');
    }

    public function index(): void {
        $categories = $this->categoryModel->getAllCategories();
        
        $data = [
            'title' => 'تصنيفات المخزون',
            'categories' => $categories,
            'breadcrumb' => [
                ['label' => 'المخازن', 'url' => '#'],
                ['label' => 'التصنيفات', 'url' => 'category/index']
            ]
        ];
        
        ob_start();
        $this->view('categories/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يرجى إدخال اسم التصنيف.');
            } elseif ($this->categoryModel->createCategory($data)) {
                $this->setFlash('success', 'تم إضافة التصنيف بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ التصنيف.');
            }
        }
        $this->redirect('category/index');
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->categoryModel->deleteCategory((int)$id)) {
                    $this->setFlash('success', 'تم حذف التصنيف.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن الحذف. يوجد منتجات مرتبطة بهذا التصنيف.');
            }
        }
        $this->redirect('category/index');
    }
}