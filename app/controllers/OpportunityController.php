<?php
// app/controllers/OpportunityController.php

class OpportunityController extends Controller {
    
    private Opportunity $oppModel;

    public function __construct() {
        $this->requireAuth();
        $this->oppModel = $this->model('Opportunity');
    }

public function index(): void {
        $opportunities = $this->oppModel->getAllOpportunities();
        
        // تصنيف الفرص لعرضها في مسار المبيعات (Pipeline)
        $groupedOpps = [
            'qualification' => [],
            'proposal' => [],
            'negotiation' => [],
            'closed_won' => [],
            'closed_lost' => []
        ];
        
        $pipelineValue = 0;
        
        foreach ($opportunities as $opp) {
            if (isset($groupedOpps[$opp->stage])) {
                $groupedOpps[$opp->stage][] = $opp;
                // حساب القيمة الإجمالية للفرص النشطة فقط
                if (in_array($opp->stage, ['qualification', 'proposal', 'negotiation', 'closed_won'])) {
                    $pipelineValue += $opp->estimated_value;
                }
            }
        }
        
        $data = [
            'title' => 'الفرص البيعية (Sales Pipeline)', 
            'groupedOpps' => $groupedOpps,
            'pipelineValue' => $pipelineValue,
            'breadcrumb' => [
                ['label' => 'المبيعات و CRM', 'url' => '#'],
                ['label' => 'الفرص البيعية', 'url' => 'opportunity/index']
            ]
        ];
        
        ob_start();
        $this->view('opportunity/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }


    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'title'               => trim($_POST['title'] ?? ''),
                'customer_id'         => (int)($_POST['customer_id'] ?? 0),
                'stage'               => trim($_POST['stage'] ?? 'qualification'),
                'estimated_value'     => (float)($_POST['estimated_value'] ?? 0),
                'probability'         => (int)($_POST['probability'] ?? 50),
                'expected_close_date' => trim($_POST['expected_close_date'] ?? ''),
                'assigned_to'         => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'description'         => trim($_POST['description'] ?? '')
            ];

            if (empty($data['title']) || empty($data['customer_id'])) {
                $this->setFlash('error', 'يجب إدخال عنوان الفرصة واختيار العميل.');
                $this->redirect('opportunity/create');
            }

            if ($this->oppModel->createOpportunity($data)) {
                $this->setFlash('success', 'تم إضافة الفرصة بنجاح.');
                $this->redirect('opportunity/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                $this->redirect('opportunity/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = ['title' => 'إضافة فرصة بيعية', 'customers' => $customers, 'users' => $users];
            
            ob_start();
            $this->view('opportunity/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('opportunity/index');
        
        $oppId = (int)$id;
        $opportunity = $this->oppModel->getOpportunityById($oppId);

        if (!$opportunity) {
            $this->setFlash('error', 'الفرصة المطلوبة غير موجودة.');
            $this->redirect('opportunity/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'title'               => trim($_POST['title'] ?? ''),
                'customer_id'         => (int)($_POST['customer_id'] ?? 0),
                'stage'               => trim($_POST['stage'] ?? 'qualification'),
                'estimated_value'     => (float)($_POST['estimated_value'] ?? 0),
                'probability'         => (int)($_POST['probability'] ?? 50),
                'expected_close_date' => trim($_POST['expected_close_date'] ?? ''),
                'assigned_to'         => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'description'         => trim($_POST['description'] ?? '')
            ];

            if (empty($data['title']) || empty($data['customer_id'])) {
                $this->setFlash('error', 'يجب إدخال عنوان الفرصة واختيار العميل.');
                $this->redirect('opportunity/edit/' . $oppId);
            }

            if ($this->oppModel->updateOpportunity($oppId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات الفرصة البيعية بنجاح.');
                $this->redirect('opportunity/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('opportunity/edit/' . $oppId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = ['title' => 'تعديل فرصة بيعية', 'opportunity' => $opportunity, 'customers' => $customers, 'users' => $users];
            
            ob_start();
            $this->view('opportunity/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->oppModel->deleteOpportunity((int)$id)) {
                $this->setFlash('success', 'تم حذف الفرصة البيعية.');
            } else {
                $this->setFlash('error', 'فشل في حذف الفرصة.');
            }
        }
        $this->redirect('opportunity/index');
    }
     public function updateStageAjax(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $id = (int)($_POST['id'] ?? 0);
            $stage = trim($_POST['stage'] ?? '');
            
            $validStages = ['qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];
            
            if ($id > 0 && in_array($stage, $validStages)) {
                if ($this->oppModel->updateStage($id, $stage)) {
                    echo json_encode(['success' => true, 'message' => 'تم تحديث مسار الفرصة بنجاح']);
                    exit;
                }
            }
        }
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'فشل التحديث']);
        exit;
    }
}