<?php
// المسار: app/controllers/LeadController.php

class LeadController extends Controller {
    
    private Lead $leadModel;

    public function __construct() {
        $this->requireAuth();
        $this->leadModel = $this->model('Lead');
    }

    public function index(): void {
        $leads = $this->leadModel->getAllLeads();
        
        $data = [
            'title' => 'العملاء المحتملين (Leads)',
            'leads' => $leads,
            'flash' => $this->getFlash(),
            'breadcrumb' => [
                ['label' => 'المبيعات و CRM', 'url' => '#'],
                ['label' => 'العملاء المحتملين', 'url' => 'lead/index']
            ]
        ];
        
        ob_start();
        $this->view('leads/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'company'     => trim($_POST['company'] ?? ''),
                'email'       => trim($_POST['email'] ?? ''),
                'phone'       => trim($_POST['phone'] ?? ''),
                'source'      => trim($_POST['source'] ?? 'other'),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يرجى إدخال اسم العميل أو اسم الشركة.');
                $this->redirect('lead/create');
            }

            if ($this->leadModel->createLead($data)) {
                $this->setFlash('success', 'تم إضافة العميل المحتمل بنجاح.');
                $this->redirect('lead/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات العميل المحتمل.');
                $this->redirect('lead/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = [
                'title' => 'إضافة عميل محتمل',
                'users' => $users,
                'flash' => $this->getFlash(),
                'breadcrumb' => [
                    ['label' => 'العملاء المحتملين', 'url' => 'lead/index'],
                    ['label' => 'إضافة جديد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('leads/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('lead/index');
        }

        $leadId = (int)$id;
        $lead = $this->leadModel->getLeadById($leadId);
        
        if (!$lead) {
            $this->setFlash('error', 'العميل المحتمل غير موجود.');
            $this->redirect('lead/index');
        }

        $followUps = $this->leadModel->getFollowUps($leadId);

        $data = [
            'title' => 'ملف العميل: ' . $lead->name,
            'lead' => $lead,
            'follow_ups' => $followUps,
            'flash' => $this->getFlash(),
            'breadcrumb' => [
                ['label' => 'العملاء المحتملين', 'url' => 'lead/index'],
                ['label' => 'سجل المتابعات', 'url' => '#']
            ]
        ];

        ob_start();
        $this->view('leads/view', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    public function addFollowUp(string $leadId = ''): void {
        if ($this->isPost() && !empty($leadId) && is_numeric($leadId)) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'lead_id'          => (int)$leadId,
                'date'             => trim($_POST['date'] ?? date('Y-m-d')),
                'type'             => trim($_POST['type'] ?? 'call'),
                'notes'            => trim($_POST['notes'] ?? ''),
                'next_action_date' => trim($_POST['next_action_date'] ?? '')
            ];

            if (empty($data['notes'])) {
                $this->setFlash('error', 'تفاصيل المتابعة مطلوبة.');
            } else {
                if ($this->leadModel->addFollowUp($data)) {
                    $this->setFlash('success', 'تم توثيق المتابعة بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ المتابعة.');
                }
            }
        }
        $this->redirect('lead/show/' . $leadId);
    }

    public function changeStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $status = trim($_POST['status'] ?? '');
            if (in_array($status, ['new', 'contacted', 'qualified', 'lost', 'converted'])) {
                $this->leadModel->updateStatus((int)$id, $status);
                $this->setFlash('success', 'تم تحديث حالة العميل بنجاح.');
            }
        }
        $this->redirect('lead/show/' . $id);
    }
}