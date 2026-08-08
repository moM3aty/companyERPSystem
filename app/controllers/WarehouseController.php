<?php
// app/controllers/WarehouseController.php

class WarehouseController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $warehouseModel = $this->model('Warehouse');
        $warehouses = $warehouseModel->getAllWarehouses();
        
        $data = [
            'title' => 'المستودعات',
            'warehouses' => $warehouses,
            'flash' => $this->getFlash(),
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'المستودعات', 'url' => 'warehouse/index']
            ]
        ];
        
        ob_start();
        $this->view('warehouse/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'code' => trim($_POST['code']),
                'address' => trim($_POST['address']),
                'is_main' => isset($_POST['is_main']) ? 1 : 0,
            ];
            
            $warehouseModel = $this->model('Warehouse');
            try {
                if ($warehouseModel->createWarehouse($data)) {
                    $this->setFlash('success', 'تم إضافة المستودع بنجاح');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الإضافة');
                }
            } catch (PDOException $e) {
                // التقاط خطأ تكرار الكود
                if ($e->getCode() == 23000) {
                    $this->setFlash('error', 'عفواً، كود المستودع مستخدم مسبقاً! يرجى اختيار كود فريد.');
                } else {
                    $this->setFlash('error', 'حدث خطأ غير متوقع في قاعدة البيانات.');
                }
            }
            $this->redirect('warehouse/index');
        } else {
            $data = [
                'title' => 'إضافة مستودع جديد',
                'flash' => $this->getFlash(),
                'breadcrumb' => [
                    ['label' => 'المستودعات', 'url' => 'warehouse/index'],
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('warehouse/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('warehouse/index');
        
        $warehouseModel = $this->model('Warehouse');
        $warehouse = $warehouseModel->getWarehouseById((int)$id);
        
        if (!$warehouse) {
            $this->setFlash('error', 'المستودع غير موجود.');
            $this->redirect('warehouse/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'is_main' => isset($_POST['is_main']) ? 1 : 0,
            ];
            
            if (empty($data['name']) || empty($data['code'])) {
                $this->setFlash('error', 'يرجى إدخال اسم وكود المستودع.');
            } else {
                try {
                    if ($warehouseModel->updateWarehouse((int)$id, $data)) {
                        $this->setFlash('success', 'تم تعديل بيانات المستودع بنجاح.');
                        $this->redirect('warehouse/index');
                        return;
                    } else {
                        $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                    }
                } catch (PDOException $e) {
                    // التقاط خطأ تكرار الكود أثناء التعديل
                    if ($e->getCode() == 23000) {
                        $this->setFlash('error', 'عفواً، كود المستودع مستخدم مسبقاً لمستودع آخر! يرجى اختيار كود فريد.');
                    } else {
                        $this->setFlash('error', 'حدث خطأ غير متوقع في قاعدة البيانات.');
                    }
                }
            }
        }

        $data = [
            'title' => 'تعديل المستودع',
            'warehouse' => $warehouse,
            'flash' => $this->getFlash(),
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'المستودعات', 'url' => 'warehouse/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('warehouse/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete(string $id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $warehouseModel = $this->model('Warehouse');
            try {
                if ($warehouseModel->deleteWarehouse((int)$id)) {
                    $this->setFlash('success', 'تم حذف المستودع بنجاح.');
                } else {
                    $this->setFlash('error', 'لا يمكن حذف المستودع الرئيسي.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف هذا المستودع لارتباطه بعمليات نقل سابقة أو بضائع مخزنة فيه.');
            }
        }
        $this->redirect('warehouse/index');
    }

    public function transfers() {
        $db = Database::getInstance();
        $db->query('
            SELECT st.*, 
                   wh1.name as from_warehouse_name,
                   wh2.name as to_warehouse_name,
                   p.name as product_name,
                   u.name as requested_by_name
            FROM stock_transfers st
            LEFT JOIN warehouses wh1 ON st.from_warehouse_id = wh1.id
            LEFT JOIN warehouses wh2 ON st.to_warehouse_id = wh2.id
            LEFT JOIN products p ON st.product_id = p.id
            LEFT JOIN users u ON st.requested_by = u.id
            ORDER BY st.id DESC
        ');
        $transfers = $db->resultSet();
        
        $data = [
            'title' => 'نقل المخزون',
            'transfers' => $transfers,
            'flash' => $this->getFlash(),
            'breadcrumb' => [
                ['label' => 'المخزون', 'url' => '#'],
                ['label' => 'نقل المخزون', 'url' => 'warehouse/transfers']
            ]
        ];
        
        ob_start();
        $this->view('warehouse/transfers', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function createTransfer() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $fromWh = (int) $_POST['from_warehouse'];
            $toWh = (int) $_POST['to_warehouse'];
            $productId = (int) $_POST['product_id'];
            $quantity = (int) $_POST['quantity'];
            $notes = trim($_POST['notes'] ?? '');
            
            if ($fromWh == $toWh) {
                $this->setFlash('error', 'لا يمكن النقل لنفس المستودع');
                $this->redirect('warehouse/transfers');
            }
            
            $warehouseModel = $this->model('Warehouse');
            try {
                $transferId = $warehouseModel->transferStock(
                    $fromWh, $toWh, $productId, $quantity,
                    Session::getUserId(), $notes
                );
                $this->setFlash('success', 'تم نقل المخزون بنجاح (رقم الطلب: ' . $transferId . ')');
            } catch (Exception $e) {
                $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
            }
            $this->redirect('warehouse/transfers');
        } else {
            $db = Database::getInstance();
            $db->query('SELECT * FROM warehouses ORDER BY name');
            $warehouses = $db->resultSet();
            
            $productModel = $this->model('Product');
            $products = $productModel->getAllProducts();
            
            $data = [
                'title' => 'نقل مخزون جديد',
                'warehouses' => $warehouses,
                'products' => $products,
                'flash' => $this->getFlash(),
                'breadcrumb' => [
                    ['label' => 'نقل المخزون', 'url' => 'warehouse/transfers'],
                    ['label' => 'أمر جديد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('warehouse/create_transfer', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}