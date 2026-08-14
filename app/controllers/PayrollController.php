<?php
// app/controllers/PayrollController.php

class PayrollController extends Controller {
    
    private $payrollModel;

    public function __construct() {
        $this->requireAuth();
        $this->requireAnyRole(['admin', 'hr', 'super_admin']);
        $this->payrollModel = $this->model('Payroll');
    }

    public function index(): void {
        $payrolls = $this->payrollModel->getAllPayrolls();
        $data = [
            'title' => 'مسيرات الرواتب (Payroll)',
            'payrolls' => $payrolls,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'مسيرات الرواتب', 'url' => 'payroll/index']]
        ];
        ob_start(); $this->view('payroll/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function generate(): void {
        if ($this->isPost()) {
            $month = (int)($_POST['month'] ?? date('n'));
            $year = (int)($_POST['year'] ?? date('Y'));

            if ($this->payrollModel->checkExists($month, $year)) {
                $this->setFlash('error', 'يوجد مسير رواتب مسجل مسبقاً لهذا الشهر! يرجى حذفه أولاً إذا أردت إعادة التوليد.');
            } else {
                $payrollId = $this->payrollModel->generatePayroll($month, $year, Session::getUserId());
                if ($payrollId) {
                    $this->setFlash('success', 'تم توليد مسير الرواتب بنجاح وبشكل آلي.');
                    $this->redirect('payroll/show/' . $payrollId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ. تأكد من وجود موظفين "نشطين" في النظام لتوليد رواتبهم.');
                }
            }
        }
        $this->redirect('payroll/index');
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('payroll/index');
        
        $payrollId = (int)$id;
        $payroll = $this->payrollModel->getPayrollById($payrollId);
        
        if (!$payroll) {
            $this->setFlash('error', 'مسير الرواتب غير موجود.');
            $this->redirect('payroll/index');
        }

        $details = $this->payrollModel->getPayrollDetails($payrollId);

        $data = [
            'title' => 'مسير رواتب شهر ' . $payroll->month . ' / ' . $payroll->year,
            'payroll' => $payroll,
            'details' => $details,
            'breadcrumb' => [['label' => 'الرواتب', 'url' => 'payroll/index'], ['label' => 'تفاصيل المسير', 'url' => '#']]
        ];
        
        ob_start(); $this->view('payroll/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function updateStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $status = trim($_POST['status'] ?? '');
            if (in_array($status, ['approved', 'paid'])) {
                $this->payrollModel->updateStatus((int)$id, $status);
                
                // 🟢 هنا يمكن إضافة القيد المحاسبي لرواتب الموظفين آلياً لاحقاً 🟢
                if ($status === 'approved') {
                    $this->setFlash('success', 'تم اعتماد المسير. جاهز للصرف.');
                } elseif ($status === 'paid') {
                    $this->setFlash('success', 'تم تسجيل المسير كـ مدفوع ونهائي.');
                }
            }
        }
        $this->redirect('payroll/show/' . $id);
    }

    public function delete(string $id = ''): void {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $payroll = $this->payrollModel->getPayrollById((int)$id);
            if ($payroll && $payroll->status === 'paid') {
                $this->setFlash('error', 'لا يمكن حذف مسير رواتب تم صرفه بالفعل وتأكيده.');
            } else {
                $this->payrollModel->deletePayroll((int)$id);
                $this->setFlash('success', 'تم مسح مسير الرواتب بنجاح.');
            }
        }
        $this->redirect('payroll/index');
    }
}