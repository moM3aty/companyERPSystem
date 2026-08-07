<?php
// app/controllers/CompanyController.php

class CompanyController extends Controller {

    private Company $companyModel;

    public function __construct() {
        // حماية مخصصة: هذا القسم متاح فقط لمدير النظام الشامل
        if (!Session::isLoggedIn() || Session::getUserRole() !== 'super_admin') {
            Session::setFlash('error', 'عفواً، لا تملك الصلاحيات الكافية للوصول إلى إدارة النظام الشامل (SaaS).');
            $this->redirect('dashboard/index');
        }
        $this->companyModel = $this->model('Company');
    }

    public function index(): void {
        $companies = $this->companyModel->getAllCompanies();
        
        $stats = ['total' => count($companies), 'active' => 0, 'suspended' => 0];
        foreach ($companies as $c) {
            if ($c->status === 'active') $stats['active']++;
            else $stats['suspended']++;
        }

        $data = [
            'title' => 'لوحة إدارة الشركات (SaaS)',
            'companies' => $companies,
            'stats' => $stats,
            'breadcrumb' => [
                ['label' => 'إدارة النظام', 'url' => '#'],
                ['label' => 'الشركات', 'url' => 'company/index']
            ]
        ];

        ob_start();
        $this->view('companies/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $companyData = [
                'name' => trim($_POST['company_name'] ?? ''),
                'domain' => trim($_POST['domain'] ?? ''),
                'email' => trim($_POST['company_email'] ?? ''),
                'phone' => trim($_POST['company_phone'] ?? ''),
                'status' => $_POST['status'] ?? 'active',
                'subscription_ends_at' => !empty($_POST['subscription_ends_at']) ? $_POST['subscription_ends_at'] : null
            ];

            $adminData = [
                'name' => trim($_POST['admin_name'] ?? ''),
                'email' => trim($_POST['admin_email'] ?? ''),
                'password' => $_POST['admin_password'] ?? ''
            ];

            if (empty($companyData['name']) || empty($adminData['email']) || empty($adminData['password'])) {
                $this->setFlash('error', 'يجب إدخال اسم الشركة، وبريد وكلمة مرور المدير الأول.');
                $this->redirect('company/create');
            }

            // التأكد من عدم تكرار إيميل المدير في النظام ككل
            $db = Database::getInstance();
            $db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
            $db->bind(':email', $adminData['email']);
            if ($db->single()) {
                $this->setFlash('error', 'البريد الإلكتروني لمدير الشركة مسجل مسبقاً في النظام.');
                $this->redirect('company/create');
            }

            if ($this->companyModel->createCompanyWithAdmin($companyData, $adminData)) {
                $this->setFlash('success', 'تم إنشاء الشركة وتوليد حساب المدير بنجاح.');
                $this->redirect('company/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإنشاء.');
                $this->redirect('company/create');
            }
        } else {
            $data = [
                'title' => 'تسجيل شركة جديدة',
                'breadcrumb' => [
                    ['label' => 'الشركات', 'url' => 'company/index'],
                    ['label' => 'إضافة شركة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('companies/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function toggleStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $status = trim($_POST['status'] ?? 'active');
            if (in_array($status, ['active', 'suspended'])) {
                $this->companyModel->toggleStatus((int)$id, $status);
                $this->setFlash('success', 'تم تحديث حالة الشركة بنجاح.');
            }
        }
        $this->redirect('company/index');
    }
}