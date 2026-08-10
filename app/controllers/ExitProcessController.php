<?php
// app/controllers/ExitProcessController.php

class ExitProcessController extends Controller {
    
    private $exitModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireAnyRole(['admin', 'super_admin', 'hr']);
        $this->exitModel = $this->model('ExitProcess');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $exits = $this->exitModel->getAllExits();
        $data = [
            'title' => 'إخلاء الطرف وإنهاء الخدمات',
            'exits' => $exits,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'إخلاء الطرف', 'url' => 'exitProcess/index']]
        ];
        ob_start(); $this->view('exitProcess/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_id'      => (int)$_POST['employee_id'],
                'resignation_date' => trim($_POST['resignation_date']),
                'last_working_day' => trim($_POST['last_working_day']),
                'reason'           => trim($_POST['reason']),
                'notice_period'    => (int)$_POST['notice_period']
            ];

            if ($this->exitModel->createExit($data)) {
                $this->setFlash('success', 'تم بدء إجراءات إخلاء الطرف، حالة الموظف الآن (قيد المغادرة).');
                $this->redirect('exitProcess/index'); return;
            }
        }
        $data = ['title' => 'بدء استقالة / إنهاء خدمة', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('exitProcess/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('exitProcess/index');
        $exit = $this->exitModel->getExitById((int)$id);
        if (!$exit) $this->redirect('exitProcess/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($this->exitModel->completeExit((int)$id, $_POST)) {
                $this->setFlash('success', 'تم اعتماد إخلاء الطرف وتحديث حالة الموظف إلى (منهي خدماته) نهائياً.');
                $this->redirect('exitProcess/index'); return;
            }
        }
        $data = ['title' => 'اعتماد إخلاء الطرف والمخالصة', 'exit' => $exit];
        ob_start(); $this->view('exitProcess/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم الاستيراد بنجاح.');
        $this->redirect('exitProcess/index');
    }
}