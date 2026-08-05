<?php
// app/controllers/CustomerController.php

class CustomerController extends Controller {
    
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
     * قائمة العملاء مع البحث
     */
    public function index() {
        $customerModel = $this->model('Customer');

        $search = trim($_GET['search'] ?? '');
        $filter = trim($_GET['filter'] ?? 'all');

        if (!empty($search)) {
            $customers = $customerModel->searchCustomers($search);
        } else {
            $customers = $customerModel->getCustomers();
        }

        // تصفية حسب النوع (مع إعادة ترقيم المصفوفة)
        if ($filter === 'company') {
            $customers = array_values(array_filter($customers, function($c) {
                return $c->type === 'company';
            }));
        } elseif ($filter === 'individual') {
            $customers = array_values(array_filter($customers, function($c) {
                return $c->type === 'individual';
            }));
        }

        $data = [
            'title'           => 'إدارة العملاء',
            'customers'       => $customers,
            'search'          => $search,
            'filter'          => $filter,
            'total_count'     => $customerModel->getCustomerCount(),
            'total_receivables' => $customerModel->getTotalReceivables(),
            'flash'           => $this->getFlash()
        ];

        $this->view('customers/index', $data);
    }

    /**
     * إضافة عميل جديد
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual'),
                'balance' => 0,
                'notes'   => trim($_POST['notes'] ?? '')
            ];

            $errors = [];

            if (empty($data['name']) || mb_strlen($data['name']) < 3) {
                $errors[] = 'اسم العميل مطلوب (3 أحرف على الأقل)';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد الإلكتروني غير صحيحة';
            }

            $allowedTypes = ['individual', 'company'];
            if (!in_array($data['type'], $allowedTypes)) {
                $data['type'] = 'individual';
            }

            if (empty($errors)) {
                $customerModel = $this->model('Customer');
                if ($customerModel->addCustomer($data)) {
                    $this->setFlash('success', 'تم إضافة العميل "' . $data['name'] . '" بنجاح');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ البيانات');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            header('Location: ' . URL_ROOT . '/customer/create');
            exit();
        }

        $data = [
            'title' => 'إضافة عميل جديد',
            'flash' => $this->getFlash()
        ];

        $this->view('customers/create', $data);
    }

    /**
     * تعديل عميل
     */
    public function edit($id) {
        $customerModel = $this->model('Customer');
        $customer = $customerModel->getCustomerById($id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل المطلوب غير موجود');
            header('Location: ' . URL_ROOT . '/customer/index');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'notes'   => trim($_POST['notes'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual')
            ];

            $errors = [];

            if (empty($data['name']) || mb_strlen($data['name']) < 3) {
                $errors[] = 'اسم العميل مطلوب (3 أحرف على الأقل)';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد الإلكتروني غير صحيحة';
            }

            if (empty($errors)) {
                if ($customerModel->updateCustomer($data, $id)) {
                    $this->setFlash('success', 'تم تحديث بيانات العميل بنجاح');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التحديث');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            header('Location: ' . URL_ROOT . '/customer/edit/' . $id);
            exit();
        }

        $data = [
            'title'    => 'تعديل بيانات عميل',
            'customer' => $customer,
            'flash'   => $this->getFlash()
        ];

        $this->view('customers/edit', $data);
    }

    /**
     * حذف عميل
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . URL_ROOT . '/customer/index');
            exit();
        }

        $customerModel = $this->model('Customer');
        $customer = $customerModel->getCustomerById($id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل غير موجود');
            header('Location: ' . URL_ROOT . '/customer/index');
            exit();
        }

        if ($customer->balance > 0) {
            $this->setFlash('warning', 'لا يمكن حذف العميل "' . $customer->name . '" — لديه رصيد مدين بقيمة ' . number_format($customer->balance, 2) . ' ر.س');
        } else {
            if ($customerModel->deleteCustomer($id)) {
                $this->setFlash('success', 'تم حذف العميل "' . $customer->name . '" بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف');
            }
        }

        header('Location: ' . URL_ROOT . '/customer/index');
        exit();
    }

    /**
     * بحث AJAX — يُرجع JSON للقوائم المنسدلة
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

        $results = $this->model('Customer')->searchCustomers($query);

        $output = [];
        foreach ($results as $c) {
            $output[] = [
                'id'    => $c->id,
                'name'  => $c->name,
                'phone' => $c->phone,
                'type'  => $c->type
            ];
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($output);
    }

    /**
     * عرض بيانات عميل (بروفايل)
     */
    public function view($id) {
        $customerModel = $this->model('Customer');
        $accountingModel = $this->model('Accounting');

        $customer = $customerModel->getCustomerById($id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل غير موجود');
            header('Location: ' . URL_ROOT . '/customer/index');
            exit();
        }

        // جلب فواتير العميل
        $db = Database::getInstance();
        
        $db->query('
            SELECT id, invoice_number, total_amount, created_at
            FROM invoices
            WHERE customer_id = :cid
            ORDER BY id DESC
            LIMIT 50
        ');
        $db->bind(':cid', $id, PDO::PARAM_INT);
        $invoices = $db->resultSet();

        // جلب المدفوعات
        $db->query('
            SELECT p.*
            FROM payments p
            WHERE reference_type = "invoice"
              AND reference_id IN (
                  SELECT id FROM invoices WHERE customer_id = :cid2
              )
            ORDER BY created_at DESC
        ');
        $db->bind(':cid2', $id, PDO::PARAM_INT);
        $payments = $db->resultSet();

        // حساب الإجمالي المدفوع
        $totalPaid = 0;
        foreach ($payments as $p) {
            $totalPaid += (float) $p->amount;
        }

        $data = [
            'title'      => 'بيانات العميل',
            'customer'   => $customer,
            'invoices'   => $invoices,
            'payments'   => $payments,
            'total_paid'   => $totalPaid,
            'flash'      => $this->getFlash()
        ];

        $this->view('customers/view', $data);
    }
}