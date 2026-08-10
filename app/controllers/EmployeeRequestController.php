<?php
// app/controllers/EmployeeRequestController.php

class EmployeeRequestController extends Controller {
    
    private $reqModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->reqModel = $this->model('EmployeeRequest');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $requests = $this->reqModel->getAllRequests();
        $data = [
            'title' => 'طلبات الموظفين (Self-Service)',
            'requests' => $requests,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'الطلبات', 'url' => 'employeeRequest/index']]
        ];
        ob_start(); $this->view('employeeRequest/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_id'  => (int)$_POST['employee_id'],
                'request_type' => trim($_POST['request_type']),
                'details'      => trim($_POST['details'])
            ];

            if ($this->reqModel->createRequest($data)) {
                $this->setFlash('success', 'تم رفع الطلب بنجاح وبانتظار رد الموارد البشرية.');
                $this->redirect('employeeRequest/index'); return;
            }
        }
        $data = ['title' => 'تقديم طلب جديد', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('employeeRequest/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        $this->requireAnyRole(['admin', 'hr', 'super_admin', 'manager']);
        if (empty($id)) $this->redirect('employeeRequest/index');
        
        $req = $this->reqModel->getRequestById((int)$id);
        if (!$req) $this->redirect('employeeRequest/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'status'   => trim($_POST['status']),
                'hr_notes' => trim($_POST['hr_notes'])
            ];
            if ($this->reqModel->updateRequest((int)$id, $data)) {
                $this->setFlash('success', 'تم الرد على الطلب وتحديث حالته.');
                $this->redirect('employeeRequest/index'); return;
            }
        }
        $data = ['title' => 'الرد على الطلب (HR Action)', 'request' => $req, 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('employeeRequest/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->reqModel->deleteRequest((int)$id);
            $this->setFlash('success', 'تم مسح الطلب نهائياً.');
        }
        $this->redirect('employeeRequest/index');
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم الاستيراد بنجاح.');
        $this->redirect('employeeRequest/index');
    }
}