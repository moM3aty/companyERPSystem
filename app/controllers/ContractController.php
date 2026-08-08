<?php
// app/controllers/ContractController.php

class ContractController extends Controller {
    
    private $contractModel; // تم تصحيح الخطأ هنا بإزالة كلمة clone

    public function __construct() {
        $this->requireAuth();
        $this->contractModel = $this->model('Contract');
    }

    public function index() {
        $contracts = $this->contractModel->getAllContracts();
        
        $data = [
            'title' => 'إدارة العقود',
            'contracts' => $contracts,
            'breadcrumb' => [
                ['label' => 'العمليات', 'url' => '#'],
                ['label' => 'العقود', 'url' => 'contract/index']
            ]
        ];
        
        ob_start();
        $this->view('contract/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'contract_number' => trim($_POST['contract_number'] ?? 'CTR-' . time()),
                'title' => trim($_POST['title'] ?? ''),
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'value' => (float)($_POST['value'] ?? 0),
                'status' => trim($_POST['status'] ?? 'draft'),
                'description' => trim($_POST['description'] ?? '')
            ];

            if (empty($data['title'])) {
                $this->setFlash('error', 'يجب إدخال عنوان / موضوع العقد.');
            } else {
                if ($this->contractModel->createContract($data)) {
                    $this->setFlash('success', 'تم تسجيل العقد بنجاح.');
                    $this->redirect('contract/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }
        
        $data = [
            'title' => 'إضافة عقد جديد',
            'default_number' => 'CTR-' . date('Ymd') . '-' . rand(10, 99),
            'breadcrumb' => [
                ['label' => 'العقود', 'url' => 'contract/index'],
                ['label' => 'إضافة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('contract/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('contract/index');
        
        $contract = $this->contractModel->getContractById((int)$id);
        if (!$contract) {
            $this->setFlash('error', 'العقد غير موجود.');
            $this->redirect('contract/index');
        }

        $data = [
            'title' => 'تفاصيل العقد',
            'contract' => $contract,
            'breadcrumb' => [
                ['label' => 'العقود', 'url' => 'contract/index'],
                ['label' => 'عرض العقد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('contract/show', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit(string $id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('contract/index');
        
        $contractId = (int)$id;
        $contract = $this->contractModel->getContractById($contractId);
        
        if (!$contract) {
            $this->setFlash('error', 'العقد غير موجود.');
            $this->redirect('contract/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'contract_number' => trim($_POST['contract_number'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'customer_name' => trim($_POST['customer_name'] ?? ''),
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'value' => (float)($_POST['value'] ?? 0),
                'status' => trim($_POST['status'] ?? 'draft'),
                'description' => trim($_POST['description'] ?? '')
            ];

            if ($this->contractModel->updateContract($contractId, $data)) {
                $this->setFlash('success', 'تم تعديل العقد بنجاح.');
                $this->redirect('contract/index');
                return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
            }
        }

        $data = [
            'title' => 'تعديل العقد',
            'contract' => $contract,
            'breadcrumb' => [
                ['label' => 'العقود', 'url' => 'contract/index'],
                ['label' => 'تعديل', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('contract/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete(string $id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->contractModel->deleteContract((int)$id)) {
                $this->setFlash('success', 'تم حذف العقد بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('contract/index');
    }
}