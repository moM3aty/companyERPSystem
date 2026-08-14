<?php
// app/controllers/FixedAssetController.php

class FixedAssetController extends Controller {
    
    private $assetModel;
    private $employeeModel;
    private $supplierModel;

    public function __construct() {
        $this->requireAuth();
        $this->assetModel = $this->model('FixedAsset');
        if (file_exists('../app/models/Employee.php')) $this->employeeModel = $this->model('Employee');
        if (file_exists('../app/models/Supplier.php')) $this->supplierModel = $this->model('Supplier');
    }

    public function index() {
        $assets = $this->assetModel->getAllAssets();
        foreach($assets as &$a) {
            $dep = $this->assetModel->calculateExpectedDepreciation($a);
            $a->current_book_value = $dep['book_value'];
            $a->monthly_depreciation = $dep['monthly'];
        }

        $data = [
            'title' => 'إدارة الأصول الثابتة (Fixed Assets)', 
            'assets' => $assets,
            'breadcrumb' => [['label' => 'المالية', 'url' => '#'], ['label' => 'الأصول الثابتة', 'url' => 'fixedAsset/index']]
        ];
        ob_start(); $this->view('fixedAsset/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'asset_id' => trim($_POST['asset_id'] ?? 'AST-'.time()),
                'barcode' => trim($_POST['barcode'] ?? ''),
                'asset_name' => trim($_POST['asset_name'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'purchase_date' => trim($_POST['purchase_date'] ?? date('Y-m-d')),
                'warranty_expiry' => trim($_POST['warranty_expiry'] ?? ''),
                'purchase_cost' => (float)($_POST['purchase_cost'] ?? 0),
                'salvage_value' => (float)($_POST['salvage_value'] ?? 0),
                'useful_life' => (int)($_POST['useful_life'] ?? 1),
                'supplier_id' => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'location' => trim($_POST['location'] ?? ''),
                'responsible_employee' => !empty($_POST['responsible_employee']) ? (int)$_POST['responsible_employee'] : null,
                'notes' => trim($_POST['notes'] ?? ''),
                'attachment' => null
            ];

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/assets/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['attachment'] = $fileName;
                }
            }

            if ($this->assetModel->createAsset($data)) {
                $this->setFlash('success', 'تم تسجيل الأصل الثابت بنجاح. سيبدأ حساب إهلاكه تلقائياً.');
                $this->redirect('fixedAsset/index'); return;
            }
        }

        $employees = $this->employeeModel ? $this->employeeModel->getAllEmployees() : [];
        $suppliers = $this->supplierModel ? $this->supplierModel->getAllSuppliers() : [];

        $data = ['title' => 'تسجيل أصل ثابت', 'employees' => $employees, 'suppliers' => $suppliers, 'auto_id' => 'AST-'.rand(1000,9999)];
        ob_start(); $this->view('fixedAsset/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id)) $this->redirect('fixedAsset/index');
        $asset = $this->assetModel->getAssetById($id);
        if (!$asset) $this->redirect('fixedAsset/index');

        $depreciation = $this->assetModel->calculateExpectedDepreciation($asset);

        $data = ['title' => 'ملف الأصل: ' . $asset->asset_name, 'asset' => $asset, 'depreciation' => $depreciation];
        ob_start(); $this->view('fixedAsset/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function postDepreciation($id = '') {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);
        if ($this->isPost() && !empty($id)) {
            $amount = (float)$_POST['amount'];
            $date = $_POST['date'] ?? date('Y-m-d');
            if ($amount > 0) {
                if ($this->assetModel->postDepreciationEntry((int)$id, $amount, $date)) {
                    $this->setFlash('success', 'تم تسجيل قيد الإهلاك بنجاح وتخفيض القيمة الدفترية للأصل.');
                } else {
                    $this->setFlash('error', 'حدث خطأ. تأكد من وجود حسابات (مصروف إهلاك) و (مجمع إهلاك) في دليل الحسابات.');
                }
            }
        }
        $this->redirect('fixedAsset/show/' . $id);
    }

    public function dispose($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $data = [
                'disposal_date' => $_POST['disposal_date'] ?? date('Y-m-d'),
                'disposal_value'=> (float)$_POST['disposal_value'],
                'disposal_type' => $_POST['disposal_type'] ?? 'Sold'
            ];
            $result = $this->assetModel->disposeAsset((int)$id, $data);
            if ($result['success']) {
                $msg = $result['gain_loss'] >= 0 ? 'بأرباح رأسمالية قدرها ' . number_format($result['gain_loss'], 2) : 'بخسائر رأسمالية قدرها ' . number_format(abs($result['gain_loss']), 2);
                $this->setFlash('success', 'تم استبعاد الأصل نهائياً ' . $msg);
            }
        }
        $this->redirect('fixedAsset/show/' . $id);
    }
}