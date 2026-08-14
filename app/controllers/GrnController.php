<?php
// app/controllers/GrnController.php

class GrnController extends Controller {
    
    private $grnModel;
    private $poModel;
    private $supplierModel;
    private $warehouseModel;

    public function __construct() {
        $this->requireAuth();
        $this->grnModel = $this->model('Grn');
        if (file_exists('../app/models/PurchaseOrder.php')) $this->poModel = $this->model('PurchaseOrder');
        if (file_exists('../app/models/Supplier.php')) $this->supplierModel = $this->model('Supplier');
        if (file_exists('../app/models/Warehouse.php')) $this->warehouseModel = $this->model('Warehouse');
    }

    public function index() {
        $grns = $this->grnModel->getAllGrns();
        $data = [
            'title' => 'مذكرات استلام البضائع (GRN)',
            'grns' => $grns,
            'breadcrumb' => [['label' => 'المشتريات', 'url' => '#'], ['label' => 'الاستلام (GRN)', 'url' => 'grn/index']]
        ];
        ob_start(); $this->view('grn/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create($poId = '') {
        $poData = null;
        $poItems = [];
        
        // إذا تم التحويل من أمر شراء (PO)
        if (!empty($poId) && is_numeric($poId) && $this->poModel) {
            $poData = $this->poModel->getPOById((int)$poId);
            $poItems = $this->poModel->getPOItems((int)$poId);
        }

        if ($this->isPost()) {
            $data = [
                'grn_number'    => trim($_POST['grn_number'] ?? 'GRN-'.time()),
                'po_id'         => !empty($_POST['po_id']) ? (int)$_POST['po_id'] : null,
                'supplier_id'   => (int)($_POST['supplier_id'] ?? 0),
                'warehouse_id'  => (int)($_POST['warehouse_id'] ?? 0),
                'delivery_date' => trim($_POST['delivery_date'] ?? date('Y-m-d')),
                'delivery_note' => trim($_POST['delivery_note'] ?? ''),
                'notes'         => trim($_POST['notes'] ?? ''),
                'attachment'    => null
            ];

            // Handle file upload (Delivery Note Photo)
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/grn/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $data['attachment'] = $fileName;
                }
            }

            $productIds = $_POST['product_id'] ?? [];
            $orderedQtys = $_POST['ordered_qty'] ?? [];
            $receivedQtys = $_POST['received_qty'] ?? [];
            $damagedQtys = $_POST['damaged_qty'] ?? [];
            $batches = $_POST['batch_number'] ?? [];
            $serials = $_POST['serial_number'] ?? [];
            $expiries = $_POST['expiry_date'] ?? [];
            
            $items = [];

            for ($i = 0; $i < count($productIds); $i++) {
                if (!empty($productIds[$i])) {
                    $oQty = (float)($orderedQtys[$i] ?? 0);
                    $rQty = (float)($receivedQtys[$i] ?? 0);
                    $dQty = (float)($damagedQtys[$i] ?? 0);
                    $aQty = $rQty - $dQty; // المقبول = المستلم - التالف
                    
                    if ($aQty > 0) {
                        $items[] = [
                            'product_id'   => (int)$productIds[$i],
                            'ordered_qty'  => $oQty,
                            'received_qty' => $rQty,
                            'damaged_qty'  => $dQty,
                            'accepted_qty' => $aQty,
                            'batch_number' => trim($batches[$i] ?? ''),
                            'serial_number'=> trim($serials[$i] ?? ''),
                            'expiry_date'  => trim($expiries[$i] ?? '')
                        ];
                    }
                }
            }

            if (empty($data['supplier_id']) || empty($data['warehouse_id']) || empty($items)) {
                $this->setFlash('error', 'يجب تحديد المورد والمستودع وإدخال كمية مقبولة لصنف واحد على الأقل.');
            } else {
                $grnId = $this->grnModel->createGrn($data, $items);
                if ($grnId) {
                    $this->setFlash('success', 'تم استلام البضاعة وتحديث أرصدة المخزون بنجاح.');
                    $this->redirect('grn/show/' . $grnId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        $suppliers = $this->supplierModel ? $this->supplierModel->getAllSuppliers() : [];
        $warehouses = $this->warehouseModel ? $this->warehouseModel->getAllWarehouses() : [];
        $products = [];
        if (file_exists('../app/models/Product.php')) {
            $prodModel = $this->model('Product');
            $products = $prodModel->getAllProducts();
        }

        $data = [
            'title' => 'استلام بضاعة (GRN)',
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'products' => $products,
            'po_data' => $poData,
            'po_items' => $poItems,
            'auto_grn_num' => 'GRN-' . date('Ymd') . '-' . rand(10,99),
            'breadcrumb' => [['label' => 'المشتريات', 'url' => 'grn/index'], ['label' => 'استلام', 'url' => '#']]
        ];
        ob_start(); $this->view('grn/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('grn/index');
        $grnId = (int)$id;
        
        $grn = $this->grnModel->getGrnById($grnId);
        if (!$grn) {
            $this->setFlash('error', 'مذكرة الاستلام غير موجودة.');
            $this->redirect('grn/index');
        }

        $items = $this->grnModel->getGrnItems($grnId);
        
        $data = [
            'title' => 'مذكرة استلام بضاعة #' . $grn->grn_number,
            'grn' => $grn,
            'items' => $items,
            'breadcrumb' => [['label' => 'الاستلام', 'url' => 'grn/index'], ['label' => 'عرض', 'url' => '#']]
        ];
        
        ob_start(); $this->view('grn/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}