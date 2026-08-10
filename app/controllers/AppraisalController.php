<?php
// app/controllers/AppraisalController.php

class AppraisalController extends Controller {
    
    private $appraisalModel;

    public function __construct() {
        $this->requireAuth();
        $this->appraisalModel = $this->model('Appraisal');
    }

    public function index() {
        $appraisals = $this->appraisalModel->getAllAppraisals();
        $data = [
            'title' => 'تقييم الأداء (Performance Appraisals)',
            'appraisals' => $appraisals,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'تقييم الأداء', 'url' => 'appraisal/index']]
        ];
        ob_start(); $this->view('appraisal/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $perf = (int)($_POST['performance_score'] ?? 0);
            $behav = (int)($_POST['behavior_score'] ?? 0);
            $att = (int)($_POST['attendance_score'] ?? 0);
            $total = ($perf + $behav + $att) / 3;

            $grade = 'ضعيف';
            if ($total >= 90) $grade = 'ممتاز';
            elseif ($total >= 75) $grade = 'جيد جداً';
            elseif ($total >= 60) $grade = 'جيد';

            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'evaluation_date' => trim($_POST['evaluation_date'] ?? date('Y-m-d')),
                'performance_score' => $perf,
                'behavior_score' => $behav,
                'attendance_score' => $att,
                'total_score' => $total,
                'grade' => $grade,
                'comments' => trim($_POST['comments'] ?? '')
            ];

            if (empty($data['employee_id'])) {
                $this->setFlash('error', 'يرجى اختيار الموظف.');
            } else {
                if ($this->appraisalModel->createAppraisal($data)) {
                    $this->setFlash('success', 'تم حفظ تقييم الموظف بنجاح.');
                    $this->redirect('appraisal/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ التقييم.');
                }
            }
        }
        
        $empModel = $this->model('Employee');
        $employees = [];
        if(method_exists($empModel, 'getAllEmployees')) {
            $employees = $empModel->getAllEmployees();
        }
        
        $data = [
            'title' => 'إضافة تقييم جديد',
            'employees' => $employees,
            'breadcrumb' => [['label' => 'تقييم الأداء', 'url' => 'appraisal/index'], ['label' => 'إضافة', 'url' => '#']]
        ];
        ob_start(); $this->view('appraisal/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
    
    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->appraisalModel->deleteAppraisal((int)$id);
            $this->setFlash('success', 'تم حذف التقييم.');
        }
        $this->redirect('appraisal/index');
    }

    public function importExcel() {
        if ($this->isPost()) $this->setFlash('success', 'تم استلام ملف الإكسيل. (بانتظار تفعيل مكتبة PhpSpreadsheet).');
        $this->redirect('appraisal/index');
    }
}