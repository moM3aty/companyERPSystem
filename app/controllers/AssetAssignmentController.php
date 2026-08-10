<?php
// app/controllers/AssetAssignmentController.php

class AssetAssignmentController extends Controller {
    
    private $assetModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->assetModel = $this->model('AssetAssignment');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $assets = $this->assetModel->getAllAssets();
        $data = [
            'title' => 'إدارة العهد والأصول (Assets)',
            'assets' => $assets,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'العهد', 'url' => 'assetAssignment/index']]
        ];
        ob_start(); $this->view('assetAssignment/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_id'     => (int)($_POST['employee_id'] ?? 0),
                'asset_id'        => trim($_POST['asset_id'] ?? ''),
                'asset_type'      => trim($_POST['asset_type'] ?? ''),
                'issue_date'      => trim($_POST['issue_date'] ?? date('Y-m-d')),
                'condition_given' => trim($_POST['condition_given'] ?? 'New'),
                'status'          => trim($_POST['status'] ?? 'Assigned'),
                'notes'           => trim($_POST['notes'] ?? '')
            ];

            if ($this->assetModel->createAsset($data)) {
                $this->setFlash('success', 'تم تسليم العهدة للموظف بنجاح.');
                $this->redirect('assetAssignment/index'); return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }
        $data = ['title' => 'تسليم عهدة جديدة', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('assetAssignment/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('assetAssignment/index');
        $asset = $this->assetModel->getAssetById((int)$id);
        if (!$asset) $this->redirect('assetAssignment/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_id'     => (int)($_POST['employee_id'] ?? 0),
                'asset_id'        => trim($_POST['asset_id'] ?? ''),
                'asset_type'      => trim($_POST['asset_type'] ?? ''),
                'issue_date'      => trim($_POST['issue_date'] ?? ''),
                'condition_given' => trim($_POST['condition_given'] ?? ''),
                'return_date'     => trim($_POST['return_date'] ?? ''),
                'status'          => trim($_POST['status'] ?? ''),
                'notes'           => trim($_POST['notes'] ?? '')
            ];

            if ($this->assetModel->updateAsset((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث بيانات العهدة وحالتها.');
                $this->redirect('assetAssignment/index'); return;
            }
        }
        $data = ['title' => 'تحديث العهدة / إرجاع', 'asset' => $asset, 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('assetAssignment/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->assetModel->deleteAsset((int)$id);
            $this->setFlash('success', 'تم حذف العهدة من السجل.');
        }
        $this->redirect('assetAssignment/index');
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم استلام الملف.');
        $this->redirect('assetAssignment/index');
    }
}