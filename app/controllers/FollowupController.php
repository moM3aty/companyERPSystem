<?php
// app/controllers/FollowupController.php

class FollowupController extends Controller {
    
    private $followupModel;

    public function __construct() {
        $this->requireAuth();
        $this->followupModel =$this->model('Followup');
    }

    public function index() {
        $followups = $this->followupModel->getAllFollowups();$data = [
            'title' => 'متابعات العملاء',
            'followups' => $followups,
            'breadcrumb' => [
                ['label' => 'المبيعات و CRM', 'url' => '#'],
                ['label' => 'المتابعات', 'url' => 'followup/index']
            ]
        ];
        
        ob_start();
        $this->view('followups/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {$data = [
                'lead_id' => (int)($_POST['lead_id'] ?? 0),                 'type' => trim($_POST['type'] ?? 'call'),
                'scheduled_at' => trim($_POST['scheduled_at'] ?? ''),
                'notes' => htmlspecialchars(trim($_POST['notes'] ?? ''))
            ];

            if (empty($data['lead_id']) || empty($data['scheduled_at'])) {$this->setFlash('error', 'يرجى تحديد العميل وتاريخ المتابعة.');
            } else {
                if ($this->followupModel->createFollowup($data)) {$this->setFlash('success', 'تم جدولة المتابعة بنجاح.');
                    $this->redirect('followup/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
                }
            }
        }

        // جلب قائمة العملاء المحتملين للاختيار منها
        $leadModel = $this->model('Lead');$leads = method_exists($leadModel, 'getAllLeads') ?$leadModel->getAllLeads() : [];

        $data = [
            'title' => 'جدولة متابعة جديدة',
            'leads' => $leads,
            'breadcrumb' => [
                ['label' => 'المتابعات', 'url' => 'followup/index'],
                ['label' => 'جديد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('followups/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function complete(string $id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {$this->followupModel->markAsCompleted((int)$id);$this->setFlash('success', 'تم إغلاق المتابعة بنجاح.');
        }
        $this->redirect('followup/index');
    }

    public function delete(string $id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {$this->followupModel->deleteFollowup((int)$id);$this->setFlash('success', 'تم حذف المتابعة.');
        }
        $this->redirect('followup/index');
    }
}