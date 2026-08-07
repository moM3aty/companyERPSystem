<?php
// app/controllers/FollowupController.php

class FollowupController extends Controller {
    
    private Followup $followupModel;

    public function __construct() {
        $this->requireAuth();
        $this->followupModel = $this->model('Followup');
    }

    public function index(): void {
        $followups = $this->followupModel->getAllFollowups();
        $data = ['title' => 'المتابعات والاجتماعات', 'followups' => $followups];
        
        ob_start();
        $this->view('followups/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'lead_id'        => (int)($_POST['lead_id'] ?? 0),
                'type'           => trim($_POST['type'] ?? 'call'),
                'scheduled_date' => trim($_POST['scheduled_date'] ?? ''),
                'notes'          => trim($_POST['notes'] ?? ''),
                'created_by'     => Session::getUserId()
            ];

            if (empty($data['lead_id']) || empty($data['scheduled_date'])) {
                $this->setFlash('error', 'يجب تحديد العميل المحتمل وتاريخ/وقت المتابعة.');
                $this->redirect('followup/create');
            }

            if ($this->followupModel->createFollowup($data)) {
                $this->setFlash('success', 'تمت جدولة المتابعة بنجاح.');
                $this->redirect('followup/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء جدولة المتابعة.');
                $this->redirect('followup/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, company FROM leads ORDER BY name ASC");
            $leads = $db->resultSet();
            
            $data = ['title' => 'جدولة متابعة جديدة', 'leads' => $leads];
            
            ob_start();
            $this->view('followups/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function complete(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->followupModel->markAsCompleted((int)$id)) {
                $this->setFlash('success', 'تم إنجاز المتابعة بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
            }
        }
        $this->redirect('followup/index');
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->followupModel->deleteFollowup((int)$id)) {
                $this->setFlash('success', 'تم حذف المتابعة.');
            } else {
                $this->setFlash('error', 'فشل في حذف المتابعة.');
            }
        }
        $this->redirect('followup/index');
    }
}