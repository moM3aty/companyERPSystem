<?php
// app/controllers/SuperadminController.php

class SuperadminController extends Controller {
    
    private $companyModel;
    private $userModel;

    public function __construct() {
        $this->requireAuth();
        
        if (Session::getUserRole() !== 'super_admin') {
            $this->setFlash('error', 'عفواً، هذه الصفحة مخصصة لمالك النظام فقط.');
            $this->redirect('dashboard/index');
        }

        $this->companyModel = $this->model('Company');
        $this->userModel = $this->model('User');
    }

    public function dashboard() {
        $companies = $this->companyModel->getAllCompanies();
        
        $totalCompanies = count($companies);
        $activeCompanies = 0;
        $totalRevenue = 0; 

        foreach ($companies as $c) {
            if ($c->status === 'active') {
                $activeCompanies++;
                $totalRevenue += ($c->subscription_plan === 'premium') ? 500 : (($c->subscription_plan === 'enterprise') ? 1000 : 150); 
            }
        }

        $data = [
            'title' => 'لوحة المالك (SaaS)',
            'companies' => $companies,
            'stats' => ['total' => $totalCompanies, 'active' => $activeCompanies, 'revenue' => $totalRevenue],
            'breadcrumb' => [
                ['label' => 'إدارة النظام', 'url' => '#'],
                ['label' => 'الشركات المشتركة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('superadmin/dashboard', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function createTenant() {
        if ($this->isPost()) {
            $plan = trim($_POST['subscription_plan'] ?? 'basic');
            
            $maxUsers = ($plan == 'enterprise') ? 50 : (($plan == 'premium') ? 15 : 5);
            $maxBranches = ($plan == 'enterprise') ? 10 : (($plan == 'premium') ? 3 : 1);
            $defaultModules = 'pos,inventory,accounting,crm';
            if ($plan != 'basic') $defaultModules .= ',hr,projects';

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'domain' => trim($_POST['domain'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'subscription_plan' => $plan,
                'subscription_end' => trim($_POST['subscription_end'] ?? date('Y-m-d', strtotime('+1 year'))),
                'max_users' => $maxUsers,
                'max_branches' => $maxBranches,
                'active_modules' => $defaultModules,
                'status' => 'active'
            ];

            if (!empty($data['name']) && !empty($data['email'])) {
                $companyId = $this->companyModel->createCompany($data);
                
                if ($companyId) {
                    $adminData = [
                        'name' => 'مدير النظام',
                        'email' => 'admin@' . (!empty($data['domain']) ? $data['domain'] : 'company' . $companyId . '.com'),
                        'role' => 'admin',
                        'password' => '123456',
                        'company_id' => $companyId
                    ];
                    $this->userModel->createUserForTenant($adminData);
                    $this->setFlash('success', 'تم إنشاء الشركة وتوليد حساب المدير الافتراضي بنجاح.');
                }
            }
        }
        $this->redirect('superadmin/dashboard');
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('superadmin/dashboard');
        
        $company = $this->companyModel->getCompanyById((int)$id);
        if (!$company) $this->redirect('superadmin/dashboard');

        $stats = $this->companyModel->getCompanyStats((int)$id);

        $data = [
            'title' => 'تحليل أداء الشركة: ' . $company->name,
            'company' => $company,
            'stats' => $stats,
            'breadcrumb' => [
                ['label' => 'لوحة المالك', 'url' => 'superadmin/dashboard'],
                ['label' => 'تفاصيل المشترك', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('superadmin/show', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('superadmin/dashboard');
        
        $company = $this->companyModel->getCompanyById((int)$id);
        if (!$company) $this->redirect('superadmin/dashboard');

        if ($this->isPost()) {
            $modules = $_POST['modules'] ?? [];
            $modulesStr = implode(',', $modules);

            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'domain' => trim($_POST['domain'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'subscription_plan' => trim($_POST['subscription_plan'] ?? 'basic'),
                'subscription_end' => trim($_POST['subscription_end'] ?? ''),
                'max_users' => (int)($_POST['max_users'] ?? 5),
                'max_branches' => (int)($_POST['max_branches'] ?? 1),
                'active_modules' => $modulesStr,
                'status' => trim($_POST['status'] ?? 'active')
            ];

            if ($this->companyModel->updateCompany((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث إعدادات الشركة وصلاحياتها بنجاح.');
                $this->redirect('superadmin/show/' . $id);
                return;
            }
        }

        $data = [
            'title' => 'التحكم الدقيق في الشركة',
            'company' => $company,
            'breadcrumb' => [
                ['label' => 'لوحة المالك', 'url' => 'superadmin/dashboard'],
                ['label' => 'تعديل الصلاحيات', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('superadmin/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function toggleStatus($id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $company = $this->companyModel->getCompanyById((int)$id);
            if ($company) {
                $newStatus = ($company->status === 'active') ? 'suspended' : 'active';
                $this->companyModel->updateStatus((int)$id, $newStatus);
                $this->setFlash('success', 'تم التحديث.');
            }
        }
        $this->redirect('superadmin/dashboard');
    }

    public function deleteTenant($id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $compId = (int)$id;
            
            if ($compId === 1) {
                $this->setFlash('error', 'إجراء غير مصرح: لا يمكنك حذف حساب مالك النظام الأساسي!');
            } else {
                if ($this->companyModel->hardDeleteCompany($compId)) {
                    $this->setFlash('success', 'تم محو الشركة ومسح كافة بياناتها من السيرفر نهائياً.');
                } else {
                    $this->setFlash('error', 'حدث خطأ خطير أثناء محاولة محو الشركة.');
                }
            }
        }
        $this->redirect('superadmin/dashboard');
    }
}