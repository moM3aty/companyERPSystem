<?php
// app/controllers/SanctionController.php

class SanctionController extends Controller {
    
    private Sanction $sanctionModel;

    public function __construct() {
        $this->requireAuth();
        $this->sanctionModel = $this->model('Sanction');
    }

    public function index(): void {
        $sanctions = $this->sanctionModel->getAllSanctions();
        $totalDeductions = $this->sanctionModel->getTotalDeductions();
        $warningsCount = $this->sanctionModel->getWarningsCount();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'الجزاءات والمخالفات',
            'sanctions' => $sanctions,
            'total_deductions' => $totalDeductions,
            'warnings_count' => $warningsCount,
            'is_admin' => $isAdmin,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الجزاءات', 'url' => 'sanction/index']
            ]
        ];
        
        ob_start();
        $this->view('sanctions/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $type = $_POST['type'] ?? 'warning';
            $amount = $type === 'deduction' ? (float)($_POST['amount'] ?? 0) : 0.00;
            
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? 0),
                'type'        => $type,
                'amount'      => $amount,
                'date'        => trim($_POST['date'] ?? date('Y-m-d')),
                'reason'      => trim($_POST['reason'] ?? ''),
                'created_by'  => Session::getUserId()
            ];

            if (empty($data['employee_id']) || empty($data['reason'])) {
                $this->setFlash('error', 'يرجى تعبئة كافة الحقول المطلوبة.');
                $this->redirect('sanction/create');
            }

            if ($this->sanctionModel->createSanction($data)) {
                $this->setFlash('success', 'تم توقيع الجزاء وتوثيقه في السجل بنجاح.');
                $this->redirect('sanction/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الجزاء.');
                $this->redirect('sanction/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'توقيع جزاء جديد',
                'employees' => $employees,
                'breadcrumb' => [
                    ['label' => 'الجزاءات', 'url' => 'sanction/index'],
                    ['label' => 'إضافة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('sanctions/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);

        if (empty($id) || !is_numeric($id)) $this->redirect('sanction/index');

        $sanctionId = (int)$id;
        $sanction = $this->sanctionModel->getSanctionById($sanctionId);

        if (!$sanction) {
            $this->setFlash('error', 'الجزاء المطلوب غير موجود.');
            $this->redirect('sanction/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $type = $_POST['type'] ?? 'warning';
            $amount = $type === 'deduction' ? (float)($_POST['amount'] ?? 0) : 0.00;
            
            $data = [
                'employee_id' => (int)($_POST['employee_id'] ?? $sanction->employee_id),
                'type'        => $type,
                'amount'      => $amount,
                'date'        => trim($_POST['date'] ?? $sanction->date),
                'reason'      => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['reason'])) {
                $this->setFlash('error', 'وصف المخالفة مطلوب.');
                $this->redirect('sanction/edit/' . $sanctionId);
            }

            if ($this->sanctionModel->updateSanction($sanctionId, $data)) {
                $this->setFlash('success', 'تم تعديل بيانات الجزاء بنجاح.');
                $this->redirect('sanction/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('sanction/edit/' . $sanctionId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $data = [
                'title' => 'تعديل الجزاء',
                'sanction' => $sanction,
                'employees' => $employees,
                'breadcrumb' => [
                    ['label' => 'الجزاءات', 'url' => 'sanction/index'],
                    ['label' => 'تعديل', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('sanctions/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->sanctionModel->deleteSanction((int)$id)) {
                $this->setFlash('success', 'تم إلغاء وحذف الجزاء.');
            } else {
                $this->setFlash('error', 'فشل في عملية الحذف.');
            }
        }
        $this->redirect('sanction/index');
    }
}