<?php
class WarehouseController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $warehouseModel = $this->model('Warehouse');
        $warehouses = $warehouseModel->getAll();
        
        $data = [
            'title' => 'المستودعات',
            'warehouses' => $warehouses,
            'flash' => $this->getFlash()
        ];
        $this->view('warehouse/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'code' => trim($_POST['code']),
                'address' => trim($_POST['address']),
                'is_main' => isset($_POST['is_main']) ? 1 : 0,
            ];
            
            $warehouseModel = $this->model('Warehouse');
            if ($warehouseModel->create($data)) {
                $this->setFlash('success', 'تم إضافة المستودع بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإضافة');
            }
            $this->redirect('warehouse/index');
        } else {
            $data = [
                'title' => 'إضافة مستودع جديد',
                'flash' => $this->getFlash()
            ];
            $this->view('warehouse/create', $data);
        }
    }

    // عرض طلبات نقل المخزون
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
            'flash' => $this->getFlash()
        ];
        $this->view('warehouse/transfers', $data);
    }

    public function createTransfer() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $_SESSION['user_id'], $notes
                );
                $this->setFlash('success', 'تم نقل المخزون بنجاح (رقم الطلب: ' . $transferId . ')');
            } catch (Exception $e) {
                $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
            }
            $this->redirect('warehouse/transfers');
        } else {
            // جلب المستودعات والمنتجات
            $db = Database::getInstance();
            $db->query('SELECT * FROM warehouses ORDER BY name');
            $warehouses = $db->resultSet();
            $productModel = $this->model('Product');
            $products = $productModel->getProducts();
            
            $data = [
                'title' => 'نقل مخزون جديد',
                'warehouses' => $warehouses,
                'products' => $products,
                'flash' => $this->getFlash()
            ];
            $this->view('warehouse/create_transfer', $data);
        }
    }
}