<?php
// app/controllers/PayrollController.php

class PayrollController extends Controller {
    
    private Payroll $payrollModel;

    public function __construct() {
        $this->requireAuth();
        $this->payrollModel = $this->model('Payroll');
    }

    public function index(): void {
        $payrolls = $this->payrollModel->getAllPayrolls();
        
        $data = [
            'title' => 'سجل مسيرات الرواتب',
            'payrolls' => $payrolls,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الرواتب والأجور', 'url' => 'payroll/index']
            ]
        ];
        
        ob_start();
        $this->view('payroll/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        $this->requireAnyRole(['admin', 'manager', 'editor']);
        $db = Database::getInstance();

        // 1. تحديد الشهر والسنة المطلوب حساب رواتبهم (الافتراضي: الشهر الحالي)
        $selectedMonth = (int)($this->getQuery('month') ?: date('n'));
        $selectedYear = (int)($this->getQuery('year') ?: date('Y'));

        // 2. معالجة حفظ المسير النهائي
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $month = (int)($_POST['month'] ?? $selectedMonth);
            $year = (int)($_POST['year'] ?? $selectedYear);
            
            $empIds = $_POST['emp_ids'] ?? [];
            $baseSalaries = $_POST['base_salaries'] ?? [];
            $deductions = $_POST['deductions'] ?? [];
            $bonuses = $_POST['bonuses'] ?? [];
            $netSalaries = $_POST['net_salaries'] ?? [];

            if (empty($empIds)) {
                $this->setFlash('error', 'لا يمكن حفظ مسير رواتب فارغ. يجب تحديد موظف واحد على الأقل.');
                $this->redirect('payroll/create');
            }

            $details = [];
            $totalNetAmount = 0.0;

            foreach ($empIds as $index => $eid) {
                // التأكد من الموظف وصحته من الداتابيز
                $db->query("SELECT name FROM employees WHERE id = :id LIMIT 1");
                $db->bind(':id', $eid, PDO::PARAM_INT);
                $emp = $db->single();
                
                if (!$emp) continue;

                $net = (float)($netSalaries[$index] ?? 0);
                $totalNetAmount += $net;

                $details[] = [
                    'employee_id'   => (int)$eid,
                    'employee_name' => $emp->name,
                    'base_salary'   => (float)($baseSalaries[$index] ?? 0),
                    'deductions'    => (float)($deductions[$index] ?? 0),
                    'bonuses'       => (float)($bonuses[$index] ?? 0),
                    'net_salary'    => $net
                ];
            }

            $payrollData = [
                'month' => $month,
                'year' => $year,
                'total_net_amount' => $totalNetAmount,
                'created_by' => Session::getUserId()
            ];

            if ($this->payrollModel->createPayroll($payrollData, $details)) {
                $this->setFlash('success', "تم اعتماد مسير رواتب شهر $month/$year وإثبات القيد المحاسبي بنجاح.");
                $this->redirect('payroll/index');
            } else {
                $this->setFlash('error', 'حدث خطأ غير متوقع أثناء الحفظ.');
                $this->redirect("payroll/create?month=$month&year=$year");
            }
        } else {
            // 3. عرض الواجهة وجلب الاستقطاعات الآلية للموظفين
            $db->query("SELECT id, name, position, salary FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();

            foreach ($employees as &$emp) {
                // حساب السلف المعتمدة لهذا الشهر
                $db->query("SELECT SUM(amount) as adv_total FROM employee_advances WHERE employee_id = :eid AND deduction_month = :m AND deduction_year = :y AND status = 'approved'");
                $db->bind(':eid', $emp->id, PDO::PARAM_INT);
                $db->bind(':m', $selectedMonth, PDO::PARAM_INT);
                $db->bind(':y', $selectedYear, PDO::PARAM_INT);
                $advTotal = (float)($db->single()->adv_total ?? 0);

                // حساب الجزاءات لهذا الشهر
                $db->query("SELECT SUM(amount) as sanc_total FROM sanctions WHERE employee_id = :eid AND MONTH(date) = :m AND YEAR(date) = :y AND type = 'deduction'");
                $db->bind(':eid', $emp->id, PDO::PARAM_INT);
                $db->bind(':m', $selectedMonth, PDO::PARAM_INT);
                $db->bind(':y', $selectedYear, PDO::PARAM_INT);
                $sancTotal = (float)($db->single()->sanc_total ?? 0);

                // حساب أيام الغياب واستقطاع قيمتها (بافتراض الشهر 30 يوم)
                $db->query("SELECT COUNT(id) as absent_days FROM attendance WHERE employee_id = :eid AND MONTH(date) = :m AND YEAR(date) = :y AND status = 'absent'");
                $db->bind(':eid', $emp->id, PDO::PARAM_INT);
                $db->bind(':m', $selectedMonth, PDO::PARAM_INT);
                $db->bind(':y', $selectedYear, PDO::PARAM_INT);
                $absentDays = (int)($db->single()->absent_days ?? 0);
                
                $dailyRate = $emp->salary / 30;
                $absentDeduction = $dailyRate * $absentDays;

                // تجميع الاستقطاعات وتخزينها في الكائن لعرضها في الواجهة
                $emp->auto_deduction = $advTotal + $sancTotal + $absentDeduction;
                $emp->advances_val = $advTotal;
                $emp->sanctions_val = $sancTotal;
                $emp->absences_val = $absentDeduction;
                $emp->absent_days = $absentDays;
            }

            $data = [
                'title' => "تجهيز رواتب شهر {$selectedMonth}/{$selectedYear}",
                'employees' => $employees,
                'selected_month' => $selectedMonth,
                'selected_year' => $selectedYear,
                'breadcrumb' => [
                    ['label' => 'الرواتب', 'url' => 'payroll/index'],
                    ['label' => 'تجهيز مسير', 'url' => '#']
                ]
            ];

            ob_start();
            $this->view('payroll/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('payroll/index');

        $payrollId = (int)$id;
        $payroll = $this->payrollModel->getPayrollById($payrollId);

        if (!$payroll) {
            $this->setFlash('error', 'مسير الرواتب المطلوب غير متوفر.');
            $this->redirect('payroll/index');
        }

        $details = $this->payrollModel->getPayrollDetails($payrollId);

        $data = [
            'title' => 'كشف رواتب شهر ' . $payroll->month . ' / ' . $payroll->year,
            'payroll' => $payroll,
            'details' => $details,
            'breadcrumb' => [
                ['label' => 'الرواتب', 'url' => 'payroll/index'],
                ['label' => 'تفاصيل المسير', 'url' => '#']
            ]
        ];

        ob_start();
        $this->view('payroll/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}