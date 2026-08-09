<?php
// app/controllers/PurchaseReturnController.php

class PurchaseReturnController extends Controller {
    
    private $returnModel;

    public function __construct() {
        $this->requireAuth();
        $this->returnModel =$this->model('PurchaseReturn');
    }

    public function index() {
        $returns = $this->returnModel->getAllReturns();$data = [
            'title' => 'مرتجعات المشتريات (Purchase Returns)',
            'returns' => $returns,
            'breadcrumb' => [
                ['label' => 'المشتريات والمخازن', 'url' => '#'],
                ['label' => 'مرتجعات المشتريات', 'url' => 'purchaseReturn/index']
            ]
        ];
        
        ob_start();
        $this->view('purchaseReturn/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function show(string $id = '') {
        if (empty($id) || !is_numeric($id))$this->redirect('purchaseReturn/index');

        $returnId = (int)$id;
        $return = $this->returnModel->getReturnById($returnId);
        
        if (!$return) {$this->setFlash('error', 'المرتجع غير موجود أو تم حذفه.');
            $this->redirect('purchaseReturn/index');
        }

        $items = $this->returnModel->getReturnItems($returnId);

        $data = [
            'title' => 'عرض مرتجع مشتريات #' . $return->return_number,
            'return' => $return,
            'items' => $items,
            'breadcrumb' => [
                ['label' => 'المرتجعات', 'url' => 'purchaseReturn/index'],
                ['label' => 'عرض المرتجع', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('purchaseReturn/show', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {
            // قراءة مباشرة للحماية من أخطاء الـ Arrays
            $data = [
                'return_number' => trim($_POST['return_number'] ?? 'PRT-' . time()),
                'supplier_id'   => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'supplier_name' => htmlspecialchars(trim($_POST['supplier_name'] ?? 'مورد غير مسجل')),
                'return_date'   => trim($_POST['return_date'] ?? date('Y-m-d')),
                'status'        => trim($_POST['status'] ?? 'approved'),
                'notes'         => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            $productIds =$_POST['product_id'] ?? [];
            $quantities =$_POST['quantity'] ?? [];
            $costs =$_POST['cost'] ?? [];
            
            $items = [];$totalAmount = 0;

            for ($i = 0; $i < count($productIds);$i++) {
                if (!empty($productIds[$i])) {$qty = (float)($quantities[$i] ?? 1);
                    $cost = (float)($costs[$i] ?? 0);$subtotal = $qty * $cost;
                    $totalAmount +=$subtotal;
                    
                    $items[] = [
                        'product_id' => (int)$productIds[$i],
                        'quantity' => $qty,
                        'cost' => $cost,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $data['total_amount'] =$totalAmount;

            if (empty($items)) {$this->setFlash('error', 'يجب إضافة صنف واحد على الأقل للمرتجع.');
            } else {
                try {
                    $returnId =$this->returnModel->createReturn($data,$items);
                    if($returnId) {$this->setFlash('success', 'تم إنشاء مرتجع المشتريات وتحديث المخزون بنجاح.');
                        $this->redirect('purchaseReturn/index');
                        return;
                    } else {
                        $this->setFlash('error', 'فشل في حفظ المرتجع.');
                    }
                } catch (Exception $e) {
                    $this->setFlash('error', 'مشكلة في قاعدة البيانات: ' . $e->getMessage());
                }
            }
        }

        $productModel = $this->model('Product');$products = $productModel->getAllProducts();$supplierModel = $this->model('Supplier');$suppliers = method_exists($supplierModel, 'getAllSuppliers') ?$supplierModel->getAllSuppliers() : [];

        $data = [
            'title' => 'إنشاء مرتجع مشتريات جديد',
            'products' => $products,
            'suppliers' => $suppliers,
            'default_return_number' => 'PRT-' . date('ymd') . rand(10,99),
            'breadcrumb' => [
                ['label' => 'المرتجعات', 'url' => 'purchaseReturn/index'],
                ['label' => 'جديد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('purchaseReturn/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id))$this->redirect('purchaseReturn/index');

        $returnId = (int)$id;
        $return = $this->returnModel->getReturnById($returnId);

        if (!$return) {$this->setFlash('error', 'المرتجع غير موجود.');
            $this->redirect('purchaseReturn/index');
        }

        if ($this->isPost()) {$data = [
                'supplier_id'   => !empty($_POST['supplier_id']) ? (int)$_POST['supplier_id'] : null,
                'supplier_name' => htmlspecialchars(trim($_POST['supplier_name'] ?? 'مورد غير مسجل')),
                'return_date'   => trim($_POST['return_date'] ?? date('Y-m-d')),
                'status'        => trim($_POST['status'] ?? 'approved'),
                'notes'         => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            $productIds =$_POST['product_id'] ?? [];
            $quantities =$_POST['quantity'] ?? [];
            $costs =$_POST['cost'] ?? [];
            
            $items = [];$totalAmount = 0;

            for ($i = 0; $i < count($productIds);$i++) {
                if (!empty($productIds[$i])) {$qty = (float)($quantities[$i] ?? 1);
                    $cost = (float)($costs[$i] ?? 0);$subtotal = $qty * $cost;
                    $totalAmount +=$subtotal;
                    
                    $items[] = [
                        'product_id' => (int)$productIds[$i],
                        'quantity' => $qty,
                        'cost' => $cost,
                        'subtotal' => $subtotal
                    ];
                }
            }

            $data['total_amount'] =$totalAmount;

            if (empty($items)) {$this->setFlash('error', 'يجب إضافة صنف واحد على الأقل للتحديث.');
            } else {
                try {
                    if ($this->returnModel->updateReturn($returnId,$data, $items)) {$this->setFlash('success', 'تم تعديل بيانات المرتجع بنجاح وتحديث الأرصدة.');
                        $this->redirect('purchaseReturn/index');
                        return;
                    } else {
                        $this->setFlash('error', 'فشل أثناء التعديل.');
                    }
                } catch (Exception $e) {
                    $this->setFlash('error', 'مشكلة تمنع الحفظ: ' . $e->getMessage());
                }
            }
        }

        $items =$this->returnModel->getReturnItems($returnId);$productModel = $this->model('Product');$products = $productModel->getAllProducts();$supplierModel = $this->model('Supplier');$suppliers = method_exists($supplierModel, 'getAllSuppliers') ?$supplierModel->getAllSuppliers() : [];

        $data = [
            'title' => 'تعديل مرتجع المشتريات #' . $return->return_number,
            'return' => $return,
            'items' => $items,
            'products' => $products,
            'suppliers' => $suppliers,
            'breadcrumb' => [
                ['label' => 'المرتجعات', 'url' => 'purchaseReturn/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('purchaseReturn/edit', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete(string $id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->returnModel->deleteReturn((int)$id)) {$this->setFlash('success', 'تم حذف المرتجع واسترداد المخزون بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('purchaseReturn/index');
    }
}