<?php
// app/controllers/ContractController.php

class ContractController extends Controller {
    
    private Contract $contractModel;

    public function __construct() {
        $this->requireAuth();
        $this->contractModel = $this->model('Contract');
    }

    public function index(): void {
        // 🟢 إطلاق فحص العقود الذكي: سيتم توليد إشعارات إذا كان هناك عقود تنتهي قريباً
        NotificationHelper::checkExpiringContracts();
        
        $contracts = $this->contractModel->getAllContractsDetails();
        
        $data = [
            'title' => 'إدارة العقود والمواثيق',
            'contracts' => $contracts,
            'breadcrumb' => [
                ['label' => 'CRM والمشاريع', 'url' => '#'],
                ['label' => 'إدارة العقود', 'url' => 'contract/index']
            ]
        ];
        
        ob_start();
        $this->view('contracts/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $contractNumber = 'CNT-' . date('Ym') . '-' . str_pad((string)random_int(10, 999), 3, '0', STR_PAD_LEFT);
            
            $data = [
                'contract_number' => $contractNumber,
                'title' => trim($_POST['title'] ?? ''),
                'party_type' => $_POST['party_type'] ?? 'customer',
                'party_id' => !empty($_POST['party_id']) ? (int)$_POST['party_id'] : null,
                'start_date' => $_POST['start_date'] ?? null,
                'end_date' => $_POST['end_date'] ?? null,
                'value' => (float)($_POST['value'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
                'description' => trim($_POST['description'] ?? '')
            ];
            
            if (empty($data['title']) || empty($data['party_id']) || empty($data['end_date'])) {
                $this->setFlash('error', 'عنوان العقد، الطرف المعني، وتاريخ الانتهاء حقول مطلوبة.');
                $this->redirect('contract/create');
            }

            if ($this->contractModel->createContractDetails($data)) {
                $this->setFlash('success', 'تم تسجيل العقد بنجاح وإدراجه في نظام التنبيهات الآلية.');
                $this->redirect('contract/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ العقد في قاعدة البيانات.');
                $this->redirect('contract/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query('SELECT id, name FROM customers ORDER BY name ASC');
            $customers = $db->resultSet();
            $db->query('SELECT id, name FROM suppliers ORDER BY name ASC');
            $suppliers = $db->resultSet();

            $data = [
                'title' => 'تسجيل عقد جديد',
                'customers' => $customers,
                'suppliers' => $suppliers,
                'breadcrumb' => [
                    ['label' => 'العقود', 'url' => 'contract/index'],
                    ['label' => 'تسجيل عقد', 'url' => '#']
                ]
            ];

            ob_start();
            $this->view('contracts/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
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