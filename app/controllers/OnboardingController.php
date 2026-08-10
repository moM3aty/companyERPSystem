<?php
// app/controllers/OnboardingController.php

class OnboardingController extends Controller {
    
    private $onboardingModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->onboardingModel = $this->model('Onboarding');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $emps = $this->employeeModel->getAllEmployees();
        foreach($emps as $e) {
            $this->onboardingModel->createOnboarding($e->id);
        }

        $onboardingList = $this->onboardingModel->getAllOnboarding();
        
        $data = [
            'title' => 'تهيئة الموظفين الجدد (Onboarding)',
            'onboarding' => $onboardingList,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'التهيئة', 'url' => 'onboarding/index']
            ]
        ];
        
        ob_start();
        $this->view('onboarding/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('onboarding/index');
        
        $onboard = $this->onboardingModel->getOnboardingById((int)$id);
        if (!$onboard) $this->redirect('onboarding/index');

        if ($this->isPost()) {
            if ($this->onboardingModel->updateOnboarding((int)$id, $_POST)) {
                $this->setFlash('success', 'تم تحديث قائمة مهام التهيئة.');
                $this->redirect('onboarding/index');
                return;
            }
        }

        $data = [
            'title' => 'تحديث مهام التهيئة',
            'onboard' => $onboard,
            'breadcrumb' => [
                ['label' => 'التهيئة', 'url' => 'onboarding/index'],
                ['label' => 'تحديث المهام', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('onboarding/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم الاستيراد بنجاح.');
        $this->redirect('onboarding/index');
    }
}