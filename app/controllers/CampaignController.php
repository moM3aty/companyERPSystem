<?php
// المسار: app/controllers/CampaignController.php

class CampaignController extends Controller {
    
    private Campaign $campaignModel;

    public function __construct() {
        $this->requireAuth();
        $this->campaignModel = $this->model('Campaign');
    }

    public function index(): void {
        $campaigns = $this->campaignModel->getAllCampaigns();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'الحملات التسويقية (Campaigns)',
            'campaigns' => $campaigns,
            'is_admin' => $isAdmin,
            'breadcrumb' => [
                ['label' => 'العملاء CRM', 'url' => '#'],
                ['label' => 'الحملات التسويقية', 'url' => 'campaign/index']
            ]
        ];
        
        ob_start();
        $this->view('campaigns/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'type'            => trim($_POST['type'] ?? 'social'),
                'status'          => trim($_POST['status'] ?? 'planned'),
                'start_date'      => trim($_POST['start_date'] ?? date('Y-m-d')),
                'end_date'        => trim($_POST['end_date'] ?? date('Y-m-d', strtotime('+30 days'))),
                'budget'          => (float)($_POST['budget'] ?? 0.0),
                'target_audience' => trim($_POST['target_audience'] ?? ''),
                'description'     => trim($_POST['description'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يجب إدخال اسم الحملة.');
                $this->redirect('campaign/create');
            }

            if ($this->campaignModel->createCampaign($data)) {
                $this->setFlash('success', 'تم إنشاء الحملة التسويقية بنجاح.');
                $this->redirect('campaign/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الحملة.');
                $this->redirect('campaign/create');
            }
        } else {
            $data = [
                'title' => 'إنشاء حملة تسويقية',
                'breadcrumb' => [
                    ['label' => 'الحملات', 'url' => 'campaign/index'],
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('campaigns/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if (empty($id) || !is_numeric($id)) $this->redirect('campaign/index');
        
        $campaignId = (int)$id;
        $campaign = $this->campaignModel->getCampaignById($campaignId);

        if (!$campaign) {
            $this->setFlash('error', 'الحملة المطلوبة غير موجودة.');
            $this->redirect('campaign/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'            => trim($_POST['name'] ?? ''),
                'type'            => trim($_POST['type'] ?? 'social'),
                'status'          => trim($_POST['status'] ?? 'planned'),
                'start_date'      => trim($_POST['start_date'] ?? ''),
                'end_date'        => trim($_POST['end_date'] ?? ''),
                'budget'          => (float)($_POST['budget'] ?? 0.0),
                'target_audience' => trim($_POST['target_audience'] ?? ''),
                'description'     => trim($_POST['description'] ?? '')
            ];

            if (empty($data['name'])) {
                $this->setFlash('error', 'يجب إدخال اسم الحملة.');
                $this->redirect('campaign/edit/' . $campaignId);
            }

            if ($this->campaignModel->updateCampaign($campaignId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات الحملة بنجاح.');
                $this->redirect('campaign/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('campaign/edit/' . $campaignId);
            }
        } else {
            $data = [
                'title' => 'تعديل حملة تسويقية',
                'campaign' => $campaign,
                'breadcrumb' => [
                    ['label' => 'الحملات', 'url' => 'campaign/index'],
                    ['label' => 'تعديل', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('campaigns/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin'); // صلاحية المدير فقط للحذف
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->campaignModel->deleteCampaign((int)$id)) {
                $this->setFlash('success', 'تم حذف الحملة التسويقية بنجاح.');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء محاولة الحذف.');
            }
        }
        $this->redirect('campaign/index');
    }
}