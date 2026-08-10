<?php
// app/controllers/PayrollController.php

class PayrollController extends Controller {
    
    private $payrollModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireAnyRole(['admin', 'manager', 'super_admin']); // قسم حساس
        $this->payrollModel = $this->model('Payroll');
    }

    public function index() {
        $payrolls = $this->payrollModel->getAllPayrolls();
        
        $data = [
            'title' => 'مسيرات الرواتب (Payroll)',
            'payrolls' => $payrolls,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الرواتب', 'url' => 'payroll/index']
            ]
        ];
        
        ob_start();
        $this->view('payroll/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $month = (int)($_POST['month'] ?? date('n'));
            $year = (int)($_POST['year'] ?? date('Y'));

            if ($this->payrollModel->checkExists($month, $year)) {
                $this->setFlash('error', 'تم توليد مسير رواتب لهذا الشهر مسبقاً! الرجاء مراجعته من القائمة.');
                $this->redirect('payroll/index');
                return;
            }

            $payrollId = $this->payrollModel->generatePayroll($month, $year, Session::getUserId());
            
            if ($payrollId) {
                $this->setFlash('success', 'تم حساب وتوليد مسير الرواتب بنجاح.');
                $this->redirect('payroll/show/' . $payrollId);
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء المعالجة أو لا يوجد موظفين نشطين.');
                $this->redirect('payroll/index');
            }
        } else {
            $data = [
                'title' => 'توليد مسير رواتب',
                'breadcrumb' => [
                    ['label' => 'الرواتب', 'url' => 'payroll/index'],
                    ['label' => 'إصدار جديد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('payroll/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('payroll/index');
        
        $payrollId = (int)$id;
        $payroll = $this->payrollModel->getPayrollById($payrollId);
        
        if (!$payroll) {
            $this->setFlash('error', 'المسير غير موجود.');
            $this->redirect('payroll/index');
        }

        $details = $this->payrollModel->getPayrollDetails($payrollId);

        $data = [
            'title' => 'مسير رواتب: ' . $payroll->month . '/' . $payroll->year,
            'payroll' => $payroll,
            'details' => $details,
            'breadcrumb' => [
                ['label' => 'الرواتب', 'url' => 'payroll/index'],
                ['label' => 'تفاصيل المسير', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('payroll/show', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function updateStatus($id = '') {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $status = trim($_POST['status'] ?? '');
            if (in_array($status, ['approved', 'paid'])) {
                if ($this->payrollModel->updateStatus((int)$id, $status)) {
                    $this->setFlash('success', 'تم تحديث حالة المسير واعتماده.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
                }
            }
        }
        $this->redirect('payroll/show/' . $id);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $payroll = $this->payrollModel->getPayrollById((int)$id);
            if ($payroll && $payroll->status === 'draft') {
                if ($this->payrollModel->deletePayroll((int)$id)) {
                    $this->setFlash('success', 'تم حذف مسير الرواتب بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء الحذف.');
                }
            } else {
                $this->setFlash('error', 'لا يمكن حذف مسير رواتب تم اعتماده أو صرفه.');
            }
        }
        $this->redirect('payroll/index');
    }
}