<?php
// app/controllers/OpportunityController.php

class OpportunityController extends Controller {
    
    /** @var Opportunity */
    private Opportunity $oppModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->oppModel = $this->model('Opportunity');
    }

    /**
     * عرض قائمة الفرص البيعية
     */
    public function index(): void {
        $opportunities = $this->oppModel->getAllOpportunities();
        
        $data = [
            'title' => 'الفرص البيعية (CRM)',
            'opportunities' => $opportunities,
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

    /**
     * إضافة فرصة بيعية جديدة
     */
    public function create(): void {
        if ($this->isPost()) {
            // تنظيف المدخلات
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'title'               => trim($_POST['title'] ?? ''),
                'customer_id'         => (int)($_POST['customer_id'] ?? 0),
                'stage'               => trim($_POST['stage'] ?? 'qualification'),
                'estimated_value'     => (float)($_POST['estimated_value'] ?? 0.00),
                'probability'         => (int)($_POST['probability'] ?? 50),
                'expected_close_date' => trim($_POST['expected_close_date'] ?? ''),
                'assigned_to'         => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'description'         => trim($_POST['description'] ?? '')
            ];

            if (empty($data['title']) || empty($data['customer_id'])) {
                $this->setFlash('error', 'يرجى إدخال عنوان الفرصة واختيار العميل.');
                $this->redirect('opportunity/create');
            }

            if ($this->oppModel->createOpportunity($data)) {
                $this->setFlash('success', 'تم تسجيل الفرصة البيعية بنجاح.');
                $this->redirect('opportunity/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ، يرجى المحاولة مرة أخرى.');
                $this->redirect('opportunity/create');
            }

        } else {
            // جلب العملاء والمستخدمين لعرضهم في قوائم الاختيار (Select)
            $db = Database::getInstance();
            
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = [
                'title'     => 'فرصة بيعية جديدة',
                'customers' => $customers,
                'users'     => $users,
                'breadcrumb' => [
                    ['label' => 'الفرص البيعية', 'url' => 'opportunity/index'],
                    ['label' => 'فرصة جديدة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('opportunity/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * عرض تفاصيل الفرصة البيعية
     */
    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('opportunity/index');
        }

        $opportunity = $this->oppModel->getOpportunityById((int)$id);
        
        if (!$opportunity) {
            $this->setFlash('error', 'الفرصة المطلوبة غير موجودة.');
            $this->redirect('opportunity/index');
        }

        $data = [
            'title' => 'تفاصيل الفرصة',
            'opportunity' => $opportunity,
            'breadcrumb' => [
                ['label' => 'الفرص البيعية', 'url' => 'opportunity/index'],
                ['label' => 'تفاصيل الفرصة', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('opportunity/view', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }
}