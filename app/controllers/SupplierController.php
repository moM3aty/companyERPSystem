<?php
// app/controllers/SupplierController.php

class SupplierController extends Controller {
    
    private $supplierModel;

    public function __construct() {
        $this->requireAuth();
        $role = Session::getUserRole();
        if (!in_array($role, ['admin', 'super_admin', 'manager', 'accountant', 'purchasing'])) {
            $this->redirect('dashboard/index');
            exit;
        }
        $this->supplierModel = $this->model('Supplier');
    }

    public function index() {
        $suppliers = [];
        try {
            $suppliers = $this->supplierModel->getAllSuppliers();
        } catch (Throwable $e) {}

        $data = [
            'title' => 'إدارة الموردين (Suppliers)',
            'suppliers' => is_array($suppliers) ? $suppliers : [],
            'breadcrumb' => [['label' => 'المشتريات', 'url' => '#'], ['label' => 'الموردين', 'url' => 'supplier/index']]
        ];
        
        ob_start(); $this->view('supplier/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'company_name'    => trim($_POST['company_name'] ?? ''),
                'contact_person'  => trim($_POST['contact_person'] ?? ''),
                'phone'           => trim($_POST['phone'] ?? ''),
                'email'           => trim($_POST['email'] ?? ''),
                'address'         => trim($_POST['address'] ?? ''),
                'tax_number'      => trim($_POST['tax_number'] ?? ''),
                'current_balance' => (float)($_POST['current_balance'] ?? 0),
                'notes'           => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['company_name'])) {
                $this->setFlash('error', 'اسم الشركة / المورد مطلوب.');
                $this->redirect('supplier/create');
                return;
            }

            try {
                if ($this->supplierModel->createSupplier($data)) {
                    $this->setFlash('success', 'تمت إضافة المورد بنجاح.');
                    $this->redirect('supplier/index');
                    return;
                }
            } catch (Throwable $e) {
                $this->setFlash('error', 'تفاصيل الخطأ: ' . $e->getMessage());
            }
        }

        $data = ['title' => 'إضافة مورد جديد'];
        ob_start(); $this->view('supplier/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('supplier/index');
        
        $supplier = null;
        try { $supplier = $this->supplierModel->getSupplierById((int)$id); } catch (Throwable $e) {}
        
        if (!$supplier) {
            $this->setFlash('error', 'المورد غير موجود.');
            $this->redirect('supplier/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'id'             => (int)$id,
                'company_name'   => trim($_POST['company_name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone'          => trim($_POST['phone'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
                'address'        => trim($_POST['address'] ?? ''),
                'tax_number'     => trim($_POST['tax_number'] ?? ''),
                'notes'          => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['company_name'])) {
                $this->setFlash('error', 'اسم المورد مطلوب.');
            } else {
                try {
                    if ($this->supplierModel->updateSupplier($data)) {
                        $this->setFlash('success', 'تم تحديث بيانات المورد بنجاح.');
                        $this->redirect('supplier/index');
                        return;
                    }
                } catch (Throwable $e) {
                    $this->setFlash('error', 'حدث خطأ: ' . $e->getMessage());
                }
            }
        }

        $data = ['title' => 'تعديل بيانات المورد', 'supplier' => $supplier];
        ob_start(); $this->view('supplier/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin']); 
        if ($this->isPost() && !empty($id)) {
            try {
                if ($this->supplierModel->deleteSupplier((int)$id)) {
                    $this->setFlash('success', 'تم حذف المورد بنجاح.');
                }
            } catch (Throwable $e) {
                $this->setFlash('error', $e->getMessage());
            }
        }
        $this->redirect('supplier/index');
    }
}