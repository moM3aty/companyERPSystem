<?php
// المسار: app/controllers/CustomerController.php

class CustomerController extends Controller {

    private Customer $customerModel;

    public function __construct() {
        $this->requireAuth();
        $this->customerModel = $this->model('Customer');
    }

    /**
     * عرض قائمة العملاء
     */
    public function index(): void {
        $customers = $this->customerModel->getAllCustomers();
        
        $totalReceivables = 0.0;
        foreach ($customers as $c) {
            if ($c->balance > 0) $totalReceivables += $c->balance;
        }

        $data = [
            'title' => 'إدارة العملاء',
            'customers' => $customers,
            'total_receivables' => $totalReceivables
        ];

        ob_start();
        $this->view('customers/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * واجهة إضافة عميل جديد وحفظه
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual'),
                'balance' => (float)($_POST['balance'] ?? 0.0)
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يجب إدخال اسم العميل.');
                $this->redirect('customer/create');
            }

            if ($this->customerModel->createCustomer($data)) {
                $this->setFlash('success', 'تم إضافة العميل بنجاح.');
                $this->redirect('customer/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات العميل.');
                $this->redirect('customer/create');
            }
        } else {
            $data = [
                'title' => 'إضافة عميل جديد'
            ];

            ob_start();
            $this->view('customers/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * 🔴 واجهة تعديل بيانات العميل 🔴
     */
    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('customer/index');
        }

        $customerId = (int)$id;
        $customer = $this->customerModel->getCustomerById($customerId);

        if (!$customer) {
            $this->setFlash('error', 'العميل المطلوب غير موجود.');
            $this->redirect('customer/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? ''),
                'type'    => trim($_POST['type'] ?? 'individual')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'اسم العميل مطلوب.');
                $this->redirect('customer/edit/' . $customerId);
            }

            if ($this->customerModel->updateCustomer($customerId, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات العميل بنجاح.');
                $this->redirect('customer/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
                $this->redirect('customer/edit/' . $customerId);
            }
        } else {
            $data = [
                'title' => 'تعديل بيانات عميل',
                'customer' => $customer
            ];

            ob_start();
            $this->view('customers/edit', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * حذف عميل
     */
    public function delete(string $id = ''): void {
        $this->requireRole('admin'); 
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            try {
                if ($this->customerModel->deleteCustomer((int)$id)) {
                    $this->setFlash('success', 'تم حذف العميل بنجاح.');
                }
            } catch (PDOException $e) {
                $this->setFlash('error', 'لا يمكن حذف العميل لوجود حركات مالية مرتبطة به.');
            }
        }
        $this->redirect('customer/index');
    }
}