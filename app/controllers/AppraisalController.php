<?php
// app/controllers/AppraisalController.php

class AppraisalController extends Controller {
    
    /** @var Appraisal */
    private Appraisal $appraisalModel;

    public function __construct() {
        $this->requireAuth();
        $this->appraisalModel = $this->model('Appraisal');
    }

    public function index(): void {
        $appraisals = $this->appraisalModel->getAllAppraisals();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'تقييم أداء الموظفين',
            'appraisals' => $appraisals,
            'is_admin' => $isAdmin,
            'flash' => $this->getFlash()
        ];
        
        $this->view('appraisals/index', $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $perf = (int)($_POST['performance_score'] ?? 0);
            $behav = (int)($_POST['behavior_score'] ?? 0);
            $attend = (int)($_POST['attendance_score'] ?? 0);
            
            // حساب المتوسط بدقة
            $totalScore = ($perf + $behav + $attend) / 3;
            
            // تحديد التقدير العام
            $grade = match(true) {
                $totalScore >= 90 => 'ممتاز',
                $totalScore >= 80 => 'جيد جداً',
                $totalScore >= 70 => 'جيد',
                $totalScore >= 60 => 'مقبول',
                default => 'ضعيف'
            };

            $data = [
                'employee_id'       => (int)($_POST['employee_id'] ?? 0),
                'evaluation_date'   => trim($_POST['evaluation_date'] ?? date('Y-m-d')),
                'performance_score' => $perf,
                'behavior_score'    => $behav,
                'attendance_score'  => $attend,
                'total_score'       => $totalScore,
                'grade'             => $grade,
                'evaluator_id'      => Session::getUserId(),
                'comments'          => trim($_POST['comments'] ?? '')
            ];

            if (empty($data['employee_id'])) {
                $this->setFlash('error', 'يجب اختيار الموظف المراد تقييمه.');
                $this->redirect('appraisal/create');
            }

            if ($this->appraisalModel->createAppraisal($data)) {
                $this->setFlash('success', 'تم حفظ تقييم الموظف بنجاح.');
                $this->redirect('appraisal/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ التقييم.');
                $this->redirect('appraisal/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name, position FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'تقييم موظف',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            
            $this->view('appraisals/create', $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->appraisalModel->delete((int)$id)) {
                $this->setFlash('success', 'تم حذف التقييم بنجاح.');
            } else {
                $this->setFlash('error', 'فشل في حذف التقييم.');
            }
        }
        $this->redirect('appraisal/index');
    }
}