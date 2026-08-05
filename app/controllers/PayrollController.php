<?php
// app/controllers/PayrollController.php

class PayrollController extends Controller {
    
    /** @var Payroll */
    private Payroll $payrollModel;

    public function __construct() {
        $this->requireAuth();
        $this->payrollModel = $this->model('Payroll');
    }

    /**
     * عرض قائمة مسيرات الرواتب
     */
    public function index(): void {
        $payrolls = $this->payrollModel->getAllPayrolls();
        
        $data = [
            'title' => 'سجل مسيرات الرواتب',
            'payrolls' => $payrolls,
            'flash' => $this->getFlash()
        ];
        
        $this->view('payroll/index', $data);
    }

    /**
     * عرض واعتماد مسير رواتب جديد
     */
    public function create(): void {
        if ($this->isPost()) {
            $month = (int)($_POST['month'] ?? date('n'));
            $year = (int)($_POST['year'] ?? date('Y'));
            
            $empIds = $_POST['emp_ids'] ?? [];
            $baseSalaries = $_POST['base_salaries'] ?? [];
            $deductions = $_POST['deductions'] ?? [];
            $bonuses = $_POST['bonuses'] ?? [];
            $netSalaries = $_POST['net_salaries'] ?? [];

            if (empty($empIds)) {
                $this->setFlash('error', 'لا يمكن حفظ مسير رواتب فارغ. لا يوجد موظفين!');
                $this->redirect('payroll/create');
            }

            $db = Database::getInstance();
            $details = [];
            $totalNetAmount = 0.0;

            // تجميع وتوثيق بيانات كل موظف
            foreach ($empIds as $index => $eid) {
                // جلب اسم الموظف من الـ DB لضمان الدقة وتجنب التلاعب عبر الـ HTML
                $db->query("SELECT name FROM employees WHERE id = :id LIMIT 1");
                $db->bind(':id', $eid, PDO::PARAM_INT);
                $emp = $db->single();
                $empName = $emp ? $emp->name : 'موظف محذوف';

                $net = (float)($netSalaries[$index] ?? 0);
                $totalNetAmount += $net;

                $details[] = [
                    'employee_id'   => (int)$eid,
                    'employee_name' => $empName,
                    'base_salary'   => (float)($baseSalaries[$index] ?? 0),
                    'deductions'    => (float)($deductions[$index] ?? 0),
                    'bonuses'       => (float)($bonuses[$index] ?? 0),
                    'net_salary'    => $net
                ];
            }

            $data = [
                'month' => $month,
                'year' => $year,
                'total_net_amount' => $totalNetAmount
            ];

            if ($this->payrollModel->createPayroll($data, $details)) {
                $this->setFlash('success', 'تم اعتماد وإصدار مسير الرواتب بنجاح لشهر ' . $month . '/' . $year);
                $this->redirect('payroll/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء حفظ مسير الرواتب.');
                $this->redirect('payroll/create');
            }

        } else {
            // جلب قائمة الموظفين النشطين لعرضهم في الواجهة
            $db = Database::getInstance();
            $db->query("SELECT id, name, position, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();

            $data = [
                'title' => 'إصدار مسير رواتب',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];

            $this->view('payroll/create', $data);
        }
    }

    /**
     * عرض كشف رواتب محدد بتفاصيله
     */
    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('payroll/index');
        }

        $payrollId = (int)$id;
        $payroll = $this->payrollModel->getPayrollById($payrollId);

        if (!$payroll) {
            $this->setFlash('error', 'مسير الرواتب المطلوب غير متوفر.');
            $this->redirect('payroll/index');
        }

        $details = $this->payrollModel->getPayrollDetails($payrollId);

        $data = [
            'title' => 'كشف رواتب شهر ' . $payroll->month . ' لسنة ' . $payroll->year,
            'payroll' => $payroll,
            'details' => $details,
            'flash' => $this->getFlash()
        ];

        $this->view('payroll/view', $data);
    }
}