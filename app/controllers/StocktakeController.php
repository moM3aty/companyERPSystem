<?php
// app/controllers/StocktakeController.php

class StocktakeController extends Controller {
    
    private $stocktakeModel;

    public function __construct() {
        $this->requireAuth();
        $this->stocktakeModel = $this->model('Stocktake');
    }

    public function index() {
        $stocktakes = $this->stocktakeModel->getAllStocktakes();
        
        $data = [
            'title' => 'عمليات الجرد',
            'stocktakes' => $stocktakes,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'عمليات الجرد', 'url' => 'stocktake/index']
            ]
        ];
        
        ob_start();
        $this->view('stocktake/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $data = [
                'reference' => 'STK-' . date('Ymd') . '-' . rand(10, 99),
                'stocktake_date' => trim($_POST['stocktake_date'] ?? date('Y-m-d')),
                'notes' => trim($_POST['notes'] ?? '')
            ];
            
            $newId = $this->stocktakeModel->createStocktake($data);
            if ($newId) {
                $this->setFlash('success', 'تم بدء عملية جرد جديدة بنجاح.');
                $this->redirect('stocktake/show/' . $newId);
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء بدء الجرد.');
                $this->redirect('stocktake/index');
            }
        }
    }

    public function show(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('stocktake/index');
        
        $stId = (int)$id;
        $stocktake = $this->stocktakeModel->getStocktakeById($stId);
        
        if (!$stocktake) {
            $this->setFlash('error', 'عملية الجرد غير موجودة.');
            $this->redirect('stocktake/index');
        }

        $items = $this->stocktakeModel->getItems($stId);
        $productModel = $this->model('Product');
        $products = $productModel->getAllProducts();

        $data = [
            'title' => 'تفاصيل الجرد: ' . $stocktake->reference,
            'stocktake' => $stocktake,
            'items' => $items,
            'products' => $products,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'عمليات الجرد', 'url' => 'stocktake/index'],
                ['label' => 'تفاصيل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('stocktake/show', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function addItem() {
        if ($this->isPost()) {
            $stocktakeId = (int)($_POST['stocktake_id'] ?? 0);
            $productId = (int)($_POST['product_id'] ?? 0);
            $actualQty = (int)($_POST['actual_quantity'] ?? 0);
            $notes = trim($_POST['notes'] ?? '');

            $productModel = $this->model('Product');
            $product = $productModel->findById($productId);
            
            if (!$product) {
                $this->setFlash('error', 'المنتج غير موجود.');
                $this->redirect('stocktake/show/' . $stocktakeId);
                return;
            }

            $sysQty = (int)($product->quantity ?? 0);
            $variance = $actualQty - $sysQty;

            $data = [
                'stocktake_id' => $stocktakeId,
                'product_id' => $productId,
                'system_quantity' => $sysQty,
                'actual_quantity' => $actualQty,
                'variance' => $variance,
                'notes' => $notes
            ];

            if ($this->stocktakeModel->addItem($data)) {
                $this->setFlash('success', 'تم إضافة الصنف للجرد.');
            } else {
                $this->setFlash('error', 'هذا الصنف مضاف مسبقاً في هذا الجرد.');
            }
            $this->redirect('stocktake/show/' . $stocktakeId);
        }
    }

    public function removeItem(string $stocktakeId = '', string $itemId = '') {
        if ($this->isPost() && is_numeric($itemId)) {
            $this->stocktakeModel->removeItem((int)$itemId);
            $this->setFlash('success', 'تم إزالة الصنف من الجرد.');
        }
        $this->redirect('stocktake/show/' . $stocktakeId);
    }

    public function complete(string $id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->stocktakeModel->completeStocktake((int)$id)) {
                $this->setFlash('success', 'تم اعتماد الجرد وتحديث أرصدة المنتجات بنجاح!');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الاعتماد.');
            }
        }
        $this->redirect('stocktake/show/' . $id);
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('stocktake/index');
        
        $stId = (int)$id;
        $stocktake = $this->stocktakeModel->getStocktakeById($stId);
        
        if (!$stocktake) {
            $this->setFlash('error', 'عملية الجرد غير موجودة.');
            $this->redirect('stocktake/index');
        }

        if (($stocktake->status ?? '') === 'completed' && !Session::hasRole('admin')) {
            $this->setFlash('error', 'لا يمكن تعديل جرد تم اعتماده، يرجى الرجوع للإدارة.');
            $this->redirect('stocktake/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'stocktake_date' => trim($_POST['stocktake_date'] ?? ''),
                'status' => trim($_POST['status'] ?? 'draft'),
                'notes' => trim($_POST['notes'] ?? '')
            ];
            
            if ($this->stocktakeModel->updateStocktake($stId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات الجرد بنجاح.');
                $this->redirect('stocktake/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
            }
        }

        $data = [
            'title' => 'تعديل بيانات الجرد',
            'stocktake' => $stocktake,
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'عمليات الجرد', 'url' => 'stocktake/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('stocktake/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete(string $id = '') {
        $this->requireRole('admin'); 
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $stId = (int)$id;
            $stocktake = $this->stocktakeModel->getStocktakeById($stId);
            
            if ($stocktake && ($stocktake->status ?? '') === 'completed') {
                 $this->setFlash('error', 'لا يمكن حذف عملية جرد تم اعتمادها بالكامل لارتباطها بالأرصدة.');
            } else {
                if ($this->stocktakeModel->deleteStocktake($stId)) {
                    $this->setFlash('success', 'تم حذف عملية الجرد بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
                }
            }
        }
        $this->redirect('stocktake/index');
    }
}