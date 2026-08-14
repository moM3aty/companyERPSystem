<?php
// app/controllers/CustomerController.php

class CustomerController extends Controller {
    
    private $customerModel;

    public function __construct() {
        $this->requireAuth();
        $this->customerModel = $this->model('Customer');
    }

    public function index() {
        $customers = $this->customerModel->getAllCustomers();
        $data = [
            'title' => 'دليل العملاء (Accounts Receivable)',
            'customers' => $customers,
            'breadcrumb' => [['label' => 'المبيعات', 'url' => '#'], ['label' => 'العملاء', 'url' => 'customer/index']]
        ];
        ob_start(); $this->view('customer/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'customer_number' => trim($_POST['customer_number'] ?? 'CUST-'.time()),
                'name'            => trim($_POST['name'] ?? ''),
                'company_name'    => trim($_POST['company_name'] ?? ''),
                'vat_number'      => trim($_POST['vat_number'] ?? ''),
                'address'         => trim($_POST['address'] ?? ''),
                'contact_person'  => trim($_POST['contact_person'] ?? ''),
                'phone'           => trim($_POST['phone'] ?? ''),
                'email'           => trim($_POST['email'] ?? ''),
                'credit_limit'    => (float)($_POST['credit_limit'] ?? 0),
                'payment_terms'   => trim($_POST['payment_terms'] ?? ''),
                'currency'        => trim($_POST['currency'] ?? 'SAR'),
                'opening_balance' => (float)($_POST['opening_balance'] ?? 0)
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'اسم العميل مطلوب.');
            } else {
                if ($this->customerModel->createCustomer($data)) {
                    $this->setFlash('success', 'تم تسجيل العميل بنجاح.');
                    $this->redirect('customer/index'); return;
                }
            }
        }

        $data = ['title' => 'إضافة عميل جديد'];
        ob_start(); $this->view('customer/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('customer/index');
        
        $customer = $this->customerModel->getCustomerById((int)$id);
        if (!$customer) $this->redirect('customer/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'company_name'    => trim($_POST['company_name'] ?? ''),
                'vat_number'      => trim($_POST['vat_number'] ?? ''),
                'address'         => trim($_POST['address'] ?? ''),
                'contact_person'  => trim($_POST['contact_person'] ?? ''),
                'phone'           => trim($_POST['phone'] ?? ''),
                'email'           => trim($_POST['email'] ?? ''),
                'credit_limit'    => (float)($_POST['credit_limit'] ?? 0),
                'payment_terms'   => trim($_POST['payment_terms'] ?? ''),
                'currency'        => trim($_POST['currency'] ?? 'SAR')
            ];

            if ($this->customerModel->updateCustomer((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات العميل بنجاح.');
                $this->redirect('customer/index'); return;
            }
        }

        $data = ['title' => 'تعديل بيانات العميل', 'customer' => $customer];
        ob_start(); $this->view('customer/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('customer/index');
        
        $customer = $this->customerModel->getCustomerById((int)$id);
        if (!$customer) $this->redirect('customer/index');

        $statement = $this->customerModel->getCustomerStatement((int)$id);
        $aging = $this->customerModel->getCustomerAging((int)$id);

        $data = [
            'title' => 'ملف العميل: ' . $customer->name,
            'customer' => $customer,
            'statement' => $statement,
            'aging' => $aging,
            'breadcrumb' => [['label' => 'العملاء', 'url' => 'customer/index'], ['label' => 'الملف الشامل', 'url' => '#']]
        ];
        
        ob_start(); $this->view('customer/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->customerModel->deleteCustomer((int)$id);
            $this->setFlash('success', 'تم مسح العميل.');
        }
        $this->redirect('customer/index');
    }
}