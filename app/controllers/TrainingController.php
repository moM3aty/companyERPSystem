<?php
// app/controllers/TrainingController.php

class TrainingController extends Controller {
    
    private $trainingModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->trainingModel =$this->model('Training');
        $this->employeeModel =$this->model('Employee');
    }

    public function index() {
        $trainings = $this->trainingModel->getAllTrainings();$data = [
            'title' => 'التدريب والتطوير',
            'trainings' => $trainings,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'التدريب', 'url' => 'training/index']]
        ];
        ob_start(); $this->view('training/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id'     => (int)$_POST['employee_id'],
                'course_name'     => trim($_POST['course_name']),
                'provider'        => trim($_POST['provider']),
                'course_date'     => trim($_POST['course_date']),
                'expiry_date'     => trim($_POST['expiry_date']),
                'cost'            => (float)$_POST['cost'],
                'skills_acquired' => trim($_POST['skills_acquired']),
                'evaluation'      => trim($_POST['evaluation'])
            ];

            if ($this->trainingModel->createTraining($data)) {$this->setFlash('success', 'تم إضافة الدورة التدريبية لملف الموظف.');
                $this->redirect('training/index'); return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }
        $data = ['title' => 'إضافة تدريب جديد', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('training/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('training/index');$training = $this->trainingModel->getTrainingById((int)$id);
        if (!$training)$this->redirect('training/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id'     => (int)$_POST['employee_id'],
                'course_name'     => trim($_POST['course_name']),
                'provider'        => trim($_POST['provider']),
                'course_date'     => trim($_POST['course_date']),
                'expiry_date'     => trim($_POST['expiry_date']),
                'cost'            => (float)$_POST['cost'],
                'skills_acquired' => trim($_POST['skills_acquired']),
                'evaluation'      => trim($_POST['evaluation'])
            ];

            if ($this->trainingModel->updateTraining((int)$id, $data)) {$this->setFlash('success', 'تم تعديل بيانات الدورة التدريبية بنجاح.');
                $this->redirect('training/index'); return;
            }
        }
        $data = ['title' => 'تعديل التدريب', 'training' => $training, 'employees' =>$this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('training/edit', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete($id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {$this->trainingModel->deleteTraining((int)$id);$this->setFlash('success', 'تم حذف السجل.');
        }
        $this->redirect('training/index');
    }

    public function importExcel() {
        if ($this->isPost())$this->setFlash('success', 'تم استلام الملف (في انتظار مكتبة القراءة).');
        $this->redirect('training/index');
    }
}