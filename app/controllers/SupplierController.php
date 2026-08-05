<?php
// app/controllers/SupplierController.php

class SupplierController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit();
        }
    }

    private function setFlash($type, $message) {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    private function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * قائمة الموردين مع البحث والفلترة
     */
    public function index() {
        $supplierModel = $this->model('Supplier');

        $search = trim($_GET['search'] ?? '');
        $filter = trim($_GET['filter'] ?? 'all');

        if (!empty($search)) {
            $suppliers = $supplierModel->searchSuppliers($search);
        } else {
            $suppliers = $supplierModel->getSuppliers();
        }

        // تصفية حسب النوع
        if ($filter === 'company') {
            $suppliers = array_filter($suppliers, function($s) { return $s->type === 'company'; });
        } elseif ($filter === 'individual') {
            $suppliers = array_filter($suppliers, function($s) { return $s->type === 'individual'; });
        }

        $data = [
            'title'             => 'إدارة الموردين',
            'suppliers'         => $suppliers,
            'search'            => $search,
            'filter'           => $filter,
            'total_count'       => $supplierModel->getSupplierCount(),
            'total_payables'     => $supplierModel->getTotalPayables(),
            'flash'            => $this->getFlash()
        ];

        $this->view('suppliers/index', $data);
    }

    /**
     * إضافة مورد جديد
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'         => trim($_POST['name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone'        => trim($_POST['phone'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'address'      => trim($_POST['address'] ?? ''),
                'balance'      => 0,
                'notes'        => trim($_POST['notes'] ?? ''),
                'type'         => trim($_POST['type'] ?? 'company')
            ];

            // === التحقق ===
            $errors = [];

            if (empty($data['name'])) {
                $errors[] = 'اسم المورد مطلوب (3 أحرف على الأقل)';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد الإلكتروني غير صحيحة';
            }

            if (empty($errors)) {
                $supplierModel = $this->model('Supplier');
                if ($supplierModel->addSupplier($data)) {
                    $this->setFlash('success', 'تم إضافة المورد "' . $data['name'] . '" بنجاح');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ البيانات');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            header('Location: ' . URL_ROOT . '/supplier/create');
            exit();
        }

        $data = [
            'title' => 'إضافة مورد جديد',
            'flash' => $this->getFlash()
        ];

        $this->view('suppliers/create', $data);
    }

    /**
     * تعديل مورد
     */
    public function edit($id) {
        $supplierModel = $this->model('Supplier');
        $supplier = $supplierModel->getSupplierById($id);

        if (!$supplier) {
            $this->setFlash('warning', 'المورد المطلوب غير موجود');
            header('Location: ' . URL_ROOT . '/supplier/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'         => trim($_POST['name'] ?? ''),
                'contact_person' => trim($_POST['contact_person'] ?? ''),
                'phone'        => trim($_POST['phone'] ?? ''),
                'email'        => trim($_POST['email'] ?? ''),
                'address'      => trim($_POST['address'] ?? ''),
                'notes'        => trim($_POST['notes'] ?? ''),
                'type'         => trim($_POST['type'] ?? 'company')
            ];

            $errors = [];

            if (empty($data['name'])) {
                $errors[] = 'اسم المورد مطلوب (3 أحرف على الأقل)';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد غير صحيحة';
            }

            if (empty($errors)) {
                if ($supplierModel->updateSupplier($data, $id)) {
                    $this->setFlash('success', 'تم تحديث بيانات المورد "' . $data['name'] . '" بنجاح');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التحديث');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            header('Location: ' . URL_ROOT . '/supplier/edit/' . $id);
            exit();
        }

        $data = [
            'title'    => 'تعديل بيانات مورد',
            'supplier' => $supplier,
            'flash'   => $this->getFlash()
        ];

        $this->view('suppliers/edit', $data);
    }

    /**
     * حذف مورد
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/supplier/index');
            exit();
        }

        $supplierModel = $this->model('Supplier');
        $supplier = $supplierModel->getSupplierById($id);

        if (!$supplier) {
            $this->setFlash('warning', 'المورد غير موجود');
            header('Location: ' . URL_ROOT . '/supplier/index');
            exit();
        }

        // منع حذف مورد لوجود أوامر شراء فعلي
        $check = $supplierModel->deleteSupplier($id);

        if (!$check) {
            $this->setFlash('warning', 'لا يمكن حذف المورد "' . $supplier->name . '" — لديه أوامر شراء قيد التنفيذ. قم بإلغاء أوامر الشراء أولاً.');
        } else {
            if ($supplierModel->deleteSupplier($id)) {
                $this->setFlash('success', 'تم حذف المورد "' . $supplier->name . '" بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف');
            }
        }

        header('Location: ' . URL_ROOT . '/supplier/index');
        exit();
    }

    /**
     * بحث AJAX — نقطة نهاية JSON للقوائم المنسدلة في إنشاء الفاتورة
     */
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode([]);
            return;
        }

        $query = trim($_GET['q'] ?? '');
        if (empty($query) || mb_strlen($query) < 2) {
            echo json_encode([]);
            return;
        }

        $results = $this->model('Supplier')->searchSuppliers($query);

        $output = [];
        foreach ($results as $s) {
            $output[] = [
                'id'    => $s->id,
                'name'  => $s->name,
                'phone' => $s->phone,
                'type'  => $s->type
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output);
    }

    /**
     * عرض بيانات المورد (بروفايل)
     */
    public function view($id) {
        $supplierModel = $this->model('Supplier');
        $supplier = $supplierModel->getSupplierById($id);

        if (!$supplier) {
            $this->setFlash('warning', 'المورد غير موجود');
            header('Location: ' . URL_ROOT . '/supplier/index');
            exit();
        }

        // جلب أوامر الشراء
        $db = Database::getInstance();
        $db->query('
            SELECT id, po_number, total_amount, status, created_at, notes
            FROM purchase_orders
            WHERE supplier_id = :sid
            ORDER BY id DESC
        ');
        $db->bind(':sid', $id, PDO::PARAM_INT);
        $purchaseOrders = $db->resultSet();

        // جلب المدفوعات
        $db->query('
            SELECT p.*
            FROM payments p
            WHERE p.reference_type = "purchase_order"
              AND p.reference_id IN (
                  SELECT id FROM purchase_orders WHERE supplier_id = :sid
              )
            ORDER BY p.created_at DESC
        ');
        $db->bind(':sid', $id, PDO::PARAM_INT);
        $payments = $db->resultSet();

        // حساب الإجمالي المدفوع
        $totalPaid = 0;
        foreach ($payments as $p) {
            $totalPaid += (float) $p->amount;
        }

        // إجمالي المشتريات (رصيدنا لدى المورد)
        $totalPayables = $supplierModel->getTotalPayables();
        $outstanding = max($totalPayables - $totalPaid, 0);

        $data = [
            'title'        => 'بيانات المورد',
            'supplier'     => $supplier,
            'purchaseOrders' => $purchaseOrders,
            'payments'     => $payments,
            'totalPaid'    => $totalPaid,
            'totalPayables' => $totalPayables,
            'outstanding'   => $outstanding,
            'flash'       => $this->getFlash()
        ];

        $this->view('suppliers/view', $data);
    }
}