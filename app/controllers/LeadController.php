<?php
// app/controllers/LeadController.php
class LeadController extends Controller {
    private Lead $leadModel;

    public function __construct() {
        $this->requireAuth();
        $this->leadModel = $this->model('Lead');
    }

  public function index(): void {
        $leads = $this->leadModel->getAllLeads();
        
        // تصنيف العملاء لبطاقات الكانبان (Kanban Board)
        $groupedLeads = [
            'new' => [],
            'contacted' => [],
            'qualified' => [],
            'lost' => []
        ];
        
        $totalLeads = count($leads);
        
        foreach ($leads as $lead) {
            if (isset($groupedLeads[$lead->status])) {
                $groupedLeads[$lead->status][] = $lead;
            }
        }
        
        $data = [
            'title' => 'العملاء المحتملين (Leads Pipeline)', 
            'groupedLeads' => $groupedLeads,
            'totalLeads' => $totalLeads
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
                'source'      => trim($_POST['source'] ?? 'organic'),
                'status'      => trim($_POST['status'] ?? 'new'),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'اسم العميل المحتمل مطلوب.');
                $this->redirect('lead/create');
            }

            if ($this->leadModel->createLead($data)) {
                $this->setFlash('success', 'تم إضافة العميل المحتمل بنجاح.');
                $this->redirect('lead/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                $this->redirect('lead/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();
            
            $data = ['title' => 'إضافة عميل محتمل', 'users' => $users];
            ob_start();
            $this->view('leads/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('lead/index');
        
        $leadId = (int)$id;
        $lead = $this->leadModel->getLeadById($leadId);

        if (!$lead) {
            $this->setFlash('error', 'العميل المحتمل غير موجود.');
            $this->redirect('lead/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name'        => trim($_POST['name'] ?? ''),
                'company'     => trim($_POST['company'] ?? ''),
                'email'       => trim($_POST['email'] ?? ''),
                'phone'       => trim($_POST['phone'] ?? ''),
                'source'      => trim($_POST['source'] ?? 'organic'),
                'status'      => trim($_POST['status'] ?? 'new'),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                'notes'       => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'اسم العميل المحتمل مطلوب.');
                $this->redirect('lead/edit/' . $leadId);
            }

            if ($this->leadModel->updateLead($leadId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات العميل المحتمل بنجاح.');
                $this->redirect('lead/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('lead/edit/' . $leadId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();
            
            $data = ['title' => 'تعديل عميل محتمل', 'lead' => $lead, 'users' => $users];
            ob_start();
            $this->view('leads/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

  public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->leadModel->deleteLead((int)$id)) {
                $this->setFlash('success', 'تم حذف العميل المحتمل.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
            }
        }
        $this->redirect('lead/index');
    }

    public function updateStatusAjax(): void {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $id = (int)($_POST['id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            
            $validStatuses = ['new', 'contacted', 'qualified', 'lost'];
            
            if ($id > 0 && in_array($status, $validStatuses)) {
                if ($this->leadModel->updateLeadStatus($id, $status)) {
                    echo json_encode(['success' => true, 'message' => 'تم تحديث الحالة بنجاح']);
                    exit;
                }
            }
        }
        
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'فشل التحديث']);
        exit;
    }
}