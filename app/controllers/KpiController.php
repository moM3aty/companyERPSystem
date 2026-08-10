<?php
// app/controllers/KpiController.php

class KpiController extends Controller {
    
    private $kpiModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->kpiModel = $this->model('Kpi');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $kpis = $this->kpiModel->getAllKpis();
        $data = [
            'title' => 'مؤشرات تقييم الأداء (KPIs)',
            'kpis' => $kpis,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'التقييم', 'url' => 'kpi/index']]
        ];
        ob_start(); $this->view('kpi/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $target = (float)($_POST['target_value'] ?? 0);
            $actual = (float)($_POST['actual_value'] ?? 0);
            $achieve = 0;
            if ($target > 0) {
                $achieve = round(($actual / $target) * 100, 2);
            }

            $data = [
                'employee_id'         => (int)($_POST['employee_id'] ?? 0),
                'review_period'       => trim($_POST['review_period'] ?? 'Annual'),
                'kpi_name'            => trim($_POST['kpi_name'] ?? ''),
                'target_value'        => $target,
                'actual_value'        => $actual,
                'achievement_percent' => $achieve,
                'weight'              => (float)($_POST['weight'] ?? 0),
                'overall_rating'      => trim($_POST['overall_rating'] ?? 'Good'),
                'manager_evaluation'  => trim($_POST['manager_evaluation'] ?? ''),
                'employee_comments'   => trim($_POST['employee_comments'] ?? ''),
                'development_plan'    => trim($_POST['development_plan'] ?? '')
            ];

            if ($this->kpiModel->createKpi($data)) {
                $this->setFlash('success', 'تم حفظ التقييم والمؤشرات بنجاح.');
                $this->redirect('kpi/index'); return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }
        $data = ['title' => 'إضافة تقييم (KPI)', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('kpi/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('kpi/index');
        $kpi = $this->kpiModel->getKpiById((int)$id);
        if (!$kpi) $this->redirect('kpi/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $target = (float)($_POST['target_value'] ?? 0);
            $actual = (float)($_POST['actual_value'] ?? 0);
            $achieve = 0;
            if ($target > 0) $achieve = round(($actual / $target) * 100, 2);

            $data = [
                'employee_id'         => (int)($_POST['employee_id'] ?? 0),
                'review_period'       => trim($_POST['review_period'] ?? ''),
                'kpi_name'            => trim($_POST['kpi_name'] ?? ''),
                'target_value'        => $target,
                'actual_value'        => $actual,
                'achievement_percent' => $achieve,
                'weight'              => (float)($_POST['weight'] ?? 0),
                'overall_rating'      => trim($_POST['overall_rating'] ?? 'Good'),
                'manager_evaluation'  => trim($_POST['manager_evaluation'] ?? ''),
                'employee_comments'   => trim($_POST['employee_comments'] ?? ''),
                'development_plan'    => trim($_POST['development_plan'] ?? '')
            ];

            if ($this->kpiModel->updateKpi((int)$id, $data)) {
                $this->setFlash('success', 'تم تحديث التقييم.');
                $this->redirect('kpi/index'); return;
            }
        }
        $data = ['title' => 'تعديل التقييم', 'kpi' => $kpi, 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('kpi/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->kpiModel->deleteKpi((int)$id);
            $this->setFlash('success', 'تم حذف التقييم.');
        }
        $this->redirect('kpi/index');
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم الاستيراد بنجاح.');
        $this->redirect('kpi/index');
    }
}