<?php
// app/controllers/CompanyController.php

class CompanyController extends Controller {
    
    private $companyModel;

    public function __construct() {
        $this->requireAuth();
        
        if (Session::getUserRole() !== 'super_admin') {
            $this->setFlash('error', 'عفواً، هذه الصفحة مخصصة لمالك النظام فقط.');
            $this->redirect('dashboard/index');
        }

        $this->companyModel = $this->model('Company');
    }

    public function index() {
        $companies = $this->companyModel->getAllCompanies();
        
        $data = [
            'title' => 'دليل الشركات المشتركة (Tenants)',
            'companies' => $companies,
            'breadcrumb' => [
                ['label' => 'إدارة النظام (SaaS)', 'url' => 'superadmin/dashboard'],
                ['label' => 'الشركات المشتركة', 'url' => 'company/index']
            ]
        ];
        
        ob_start();
        $this->view('company/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}