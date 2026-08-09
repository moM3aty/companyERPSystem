<?php
// app/controllers/SupplierController.php

class SupplierController extends Controller {
    
    private $supplierModel;

    public function __construct() {
        $this->requireAuth();
        $this->supplierModel = $this->model('Supplier');
    }

    public function index() {
        $suppliers = $this->supplierModel->getAllSuppliers();
        
        $data = [
            'title' => 'دليل الموردين',
            'suppliers' => $suppliers,
            'breadcrumb' => [
                ['label' => 'المشتريات والمخازن', 'url' => '#'],
                ['label' => 'الموردين', 'url' => 'supplier/index']
            ]
        ];
        
        ob_start();
        $this->view('suppliers/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $data = [
                'name'       => trim($_POST['name'] ?? ''),
                'email'      => trim($_POST['email'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
                'address'    => trim($_POST['address'] ?? ''),
                'tax_number' => trim($_POST['tax_number'] ?? ''),
                'balance'    => (float)($_POST['balance'] ?? 0)
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يرجى إدخال اسم المورد الأساسي.');
            } else {
                if ($this->supplierModel->createSupplier($data)) {
                    $this->setFlash('success', 'تم إضافة المورد بنجاح.');
                    $this->redirect('supplier/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات المورد.');
                }
            }
        }

        $data = [
            'title' => 'إضافة مورد جديد',
            'breadcrumb' => [
                ['label' => 'الموردين', 'url' => 'supplier/index'],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('suppliers/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('supplier/index');
        
        $supplier = $this->supplierModel->getSupplierById((int)$id);
        
        if (!$supplier) {
            $this->setFlash('error', 'المورد غير موجود.');
            $this->redirect('supplier/index');
        }

        if ($this->isPost()) {
            $data = [
                'name'       => trim($_POST['name'] ?? ''),
                'email'      => trim($_POST['email'] ?? ''),
                'phone'      => trim($_POST['phone'] ?? ''),
                'address'    => trim($_POST['address'] ?? ''),
                'tax_number' => trim($_POST['tax_number'] ?? ''),
                'balance'    => (float)($_POST['balance'] ?? 0)
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يرجى إدخال اسم المورد.');
            } else {
                if ($this->supplierModel->updateSupplier((int)$id, $data)) {
                    $this->setFlash('success', 'تم تعديل بيانات المورد بنجاح.');
                    $this->redirect('supplier/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                }
            }
        }

        $data = [
            'title' => 'تعديل بيانات المورد',
            'supplier' => $supplier,
            'breadcrumb' => [
                ['label' => 'الموردين', 'url' => 'supplier/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('suppliers/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    // 🟢 دالة الحذف التي طلبتها 🟢
    public function delete($id = '') {
        $this->requireAnyRole(['admin', 'super_admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->supplierModel->deleteSupplier((int)$id)) {
                    $this->setFlash('success', 'تم حذف المورد من النظام بنجاح.');
                } else {
                    $this->setFlash('error', 'فشل في حذف المورد.');
                }
            } catch (PDOException $e) {
                // منع الحذف إذا كان المورد مرتبطاً بفواتير أو مشتريات
                $this->setFlash('error', 'عفواً! لا يمكن حذف المورد لارتباطه بأوامر شراء ومرتجعات مالية مسجلة في النظام.');
            }
        }
        $this->redirect('supplier/index');
    }
}