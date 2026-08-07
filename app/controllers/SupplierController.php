<?php
// المسار: app/controllers/SupplierController.php

class SupplierController extends Controller {
    
    private Supplier $supplierModel;

    public function __construct() {
        $this->requireAuth();
        $this->supplierModel = $this->model('Supplier');
    }

    public function index(): void {
        $search = $this->getQuery('search', '');
        $filter = $this->getQuery('filter', 'all');

        $suppliers = $this->supplierModel->getFilteredSuppliers($search, $filter);
        $totalPayables = $this->supplierModel->getTotalPayables();

        $data = [
            'title'          => 'إدارة الموردين',
            'suppliers'      => $suppliers,
            'search'         => $search,
            'filter'         => $filter,
            'total_payables' => $totalPayables,
            'total_count'    => count($suppliers)
        ];

        ob_start();
        $this->view('suppliers/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'           => trim($_POST['name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone'          => trim($_POST['phone'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
                'address'        => trim($_POST['address'] ?? ''),
                'balance'        => (float)($_POST['balance'] ?? 0.0),
                'notes'          => trim($_POST['notes'] ?? ''),
                'type'           => in_array($_POST['type'] ?? '', ['company', 'individual']) ? $_POST['type'] : 'company'
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'اسم المورد الأساسي لا يمكن أن يكون فارغاً.');
                $this->redirect('supplier/create');
            }

            if ($this->supplierModel->create($data)) {
                $this->setFlash('success', 'تم تسجيل المورد الجديد بنجاح في المنظومة.');
                $this->redirect('supplier/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ البيانات.');
                $this->redirect('supplier/create');
            }
        } else {
            $data = [
                'title' => 'إضافة مورد جديد'
            ];
            
            ob_start();
            $this->view('suppliers/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->setFlash('error', 'معرف المورد غير صالح.');
            $this->redirect('supplier/index');
        }

        $supplierId = (int)$id;
        $supplier = $this->supplierModel->findById($supplierId);

        if (!$supplier) {
            $this->setFlash('error', 'لم يتم العثور على المورد المطلوب.');
            $this->redirect('supplier/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $updateData = [
                'name'           => trim($_POST['name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone'          => trim($_POST['phone'] ?? ''),
                'email'          => trim($_POST['email'] ?? ''),
                'address'        => trim($_POST['address'] ?? ''),
                'notes'          => trim($_POST['notes'] ?? ''),
                'type'           => in_array($_POST['type'] ?? '', ['company', 'individual']) ? $_POST['type'] : 'company'
            ];

            if (empty($updateData['name'])) {
                $this->setFlash('error', 'يرجى تعبئة اسم المورد.');
                $this->redirect('supplier/edit/' . $supplierId);
            }

            if ($this->supplierModel->update($supplierId, $updateData)) {
                $this->setFlash('success', 'تم تحديث بيانات المورد بنجاح.');
                $this->redirect('supplier/index');
            } else {
                $this->setFlash('error', 'فشل في تحديث بيانات المورد.');
                $this->redirect('supplier/edit/' . $supplierId);
            }
        } else {
            $data = [
                'title'    => 'تعديل بيانات مورد',
                'supplier' => $supplier
            ];
            
            ob_start();
            $this->view('suppliers/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin'); // حماية
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $supplierId = (int)$id;
            try {
                if ($this->supplierModel->delete($supplierId)) {
                    $this->setFlash('success', 'تم حذف المورد من سجلات النظام.');
                } else {
                    $this->setFlash('error', 'فشل في عملية الحذف.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف هذا المورد لوجود حركات مالية مرتبطة به.');
            }
        }
        $this->redirect('supplier/index');
    }
}