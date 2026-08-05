<?php
// app/controllers/CustomerController.php

class CustomerController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول قبل الوصول لأي وظيفة في هذا المتحكم
        $this->requireAuth();
    }

    /**
     * قائمة العملاء مع البحث والفلترة
     */
    public function index() {
        $customerModel = $this->model('Customer');

        $search = trim($this->getQuery('search', ''));
        $filter = trim($this->getQuery('filter', 'all'));

        // جلب البيانات بناءً على البحث
        if (!empty($search)) {
            $customers = $customerModel->searchCustomers($search);
        } else {
            $customers = $customerModel->getCustomers();
        }

        // تصفية حسب النوع (أفراد أو شركات)
        if ($filter === 'company') {
            $customers = array_values(array_filter($customers, function($c) { return $c->type === 'company'; }));
        } elseif ($filter === 'individual') {
            $customers = array_values(array_filter($customers, function($c) { return $c->type === 'individual'; }));
        }

        // حساب إجمالي ذمم العملاء (الديون المستحقة لنا)
        $totalReceivables = 0;
        foreach ($customers as $c) {
            if ($c->balance > 0) {
                $totalReceivables += $c->balance;
            }
        }

        $data = [
            'title'             => 'إدارة العملاء',
            'customers'         => $customers,
            'search'            => $search,
            'filter'            => $filter,
            'total_count'       => count($customers),
            'total_receivables' => $totalReceivables,
            'flash'             => $this->getFlash()
        ];

        $this->view('customers/index', $data);
    }

    /**
     * إضافة عميل جديد
     */
    public function create() {
        if ($this->isPost()) {
            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'notes'   => trim($_POST['notes'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual')
            ];

            $errors = [];

            if (empty($data['name'])) {
                $errors[] = 'اسم العميل مطلوب';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد الإلكتروني غير صحيحة';
            }

            if (empty($errors)) {
                $customerModel = $this->model('Customer');
                if ($customerModel->addCustomer($data)) {
                    $this->setFlash('success', 'تم إضافة العميل "' . $data['name'] . '" بنجاح');
                    $this->redirect('customer/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ البيانات');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            $this->redirect('customer/create');
        }

        $data = [
            'title' => 'إضافة عميل جديد',
            'flash' => $this->getFlash()
        ];

        $this->view('customers/create', $data);
    }

    /**
     * تعديل بيانات عميل
     */
    public function edit($id) {
        $customerModel = $this->model('Customer');
        $customer = $customerModel->getCustomerById((int)$id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل المطلوب غير موجود');
            $this->redirect('customer/index');
        }

        if ($this->isPost()) {
            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'notes'   => trim($_POST['notes'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual')
            ];

            $errors = [];

            if (empty($data['name'])) {
                $errors[] = 'اسم العميل مطلوب';
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'صيغة البريد غير صحيحة';
            }

            if (empty($errors)) {
                if ($customerModel->updateCustomer($data, (int)$id)) {
                    $this->setFlash('success', 'تم تحديث بيانات العميل "' . $data['name'] . '" بنجاح');
                    $this->redirect('customer/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التحديث');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            $this->redirect('customer/edit/' . $id);
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
        // حماية من عمليات الحذف عبر روابط GET
        if (!$this->isPost()) {
            $this->redirect('customer/index');
        }

        $customerModel = $this->model('Customer');
        $customer = $customerModel->getCustomerById((int)$id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل غير موجود');
            $this->redirect('customer/index');
        }

        try {
            if ($customerModel->deleteCustomer((int)$id)) {
                $this->setFlash('success', 'تم حذف العميل "' . $customer->name . '" بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف (قد يكون للعميل فواتير مرتبطة)');
            }
        } catch (Exception $e) {
            $this->setFlash('error', 'لا يمكن الحذف لوجود سجلات مالية متعلقة بالعميل.');
        }

        $this->redirect('customer/index');
    }

    /**
     * عرض بيانات عميل (بروفايل العميل الشامل)
     */
    public function show($id) {
        $customerModel = $this->model('Customer');
        $customer = $customerModel->getCustomerById((int)$id);

        if (!$customer) {
            $this->setFlash('warning', 'العميل غير موجود');
            $this->redirect('customer/index');
        }

        // جلب الفواتير المرتبطة بالعميل
        $db = Database::getInstance();
        $db->query('
            SELECT id, invoice_number, total_amount, payment_status, created_at
            FROM invoices
            WHERE customer_id = :cid
            ORDER BY id DESC
        ');
        $db->bind(':cid', $id, PDO::PARAM_INT);
        $invoices = $db->resultSet();

        // جلب المدفوعات (إيصالات القبض من العميل)
        $db->query('
            SELECT p.*
            FROM payments p
            WHERE p.reference_type = "invoice"
              AND p.reference_id IN (
                  SELECT id FROM invoices WHERE customer_id = :cid
              )
            ORDER BY p.created_at DESC
        ');
        $db->bind(':cid', $id, PDO::PARAM_INT);
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
            'total_paid' => $totalPaid,
            'flash'      => $this->getFlash()
        ];

        // استدعاء ملف الـ view لعرض البيانات
        $this->view('customers/view', $data); 
    }
}