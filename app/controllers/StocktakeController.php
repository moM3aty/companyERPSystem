<?php
// المسار: app/controllers/StocktakeController.php

class StocktakeController extends Controller {
    
    private Stocktake $stocktakeModel;

    public function __construct() {
        $this->requireAuth();
        $this->stocktakeModel = $this->model('Stocktake');
    }

    public function index(): void {
        $adjustments = $this->stocktakeModel->getAllAdjustments();
        
        $data = [
            'title' => 'الجرد وتسويات المخزون',
            'adjustments' => $adjustments,
            'breadcrumb' => [['label' => 'المخازن', 'url' => '#'], ['label' => 'الجرد والتسويات', 'url' => 'stocktake/index']]
        ];
        
        ob_start();
        $this->view('stocktake/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'product_id' => !empty($_POST['product_id']) ? (int)$_POST['product_id'] : 0,
                'type'       => trim($_POST['type'] ?? 'addition'),
                'quantity'   => (int)($_POST['quantity'] ?? 0),
                'date'       => trim($_POST['date'] ?? date('Y-m-d')),
                'notes'      => trim($_POST['notes'] ?? ''),
                'created_by' => Session::getUserId()
            ];

            if ($data['product_id'] === 0 || $data['quantity'] <= 0) {
                $this->setFlash('error', 'يرجى اختيار الصنف وإدخال كمية صحيحة أكبر من الصفر.');
                $this->redirect('stocktake/create');
            }

            if (in_array($data['type'], ['subtraction', 'damage', 'loss'])) {
                $db = Database::getInstance();
                $db->query("SELECT quantity FROM products WHERE id = :id LIMIT 1");
                $db->bind(':id', $data['product_id'], PDO::PARAM_INT);
                $product = $db->single();
                
                if ($product && $product->quantity < $data['quantity']) {
                    $this->setFlash('error', 'كمية العجز المسجلة تتجاوز الرصيد الفعلي للصنف في المستودع.');
                    $this->redirect('stocktake/create');
                }
            }

            if ($this->stocktakeModel->createAdjustment($data)) {
                $this->setFlash('success', 'تم حفظ تسوية الجرد بنجاح وتحديث أرصدة المخزون.');
                $this->redirect('stocktake/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء معالجة التسوية المخزنية.');
                $this->redirect('stocktake/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, sku, quantity FROM products ORDER BY name ASC");
            $products = $db->resultSet();

            $data = [
                'title' => 'تسجيل حركة جرد/تسوية',
                'products' => $products,
                'breadcrumb' => [['label' => 'المخازن', 'url' => 'product/index'], ['label' => 'تسويات الجرد', 'url' => 'stocktake/index'], ['label' => 'حركة جديدة', 'url' => '#']]
            ];
            
            ob_start();
            $this->view('stocktake/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}