<?php
// app/controllers/EmployeeController.php

class EmployeeController extends Controller {
    
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $employees = $this->employeeModel->getAllEmployees();
        $data = [
            'title' => 'دليل الموظفين (Employee Master)',
            'employees' => $employees,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'الموظفين', 'url' => 'employee/index']]
        ];
        ob_start(); $this->view('employee/index', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('employee/index');
        
        $empId = (int)$id;
        $employee = $this->employeeModel->getEmployeeById($empId);
        
        if (!$employee) {
            $this->setFlash('error', 'الموظف غير موجود.');
            $this->redirect('employee/index');
        }

        $documents = $this->employeeModel->getEmployeeDocuments($empId);
        $assets    = $this->employeeModel->getEmployeeAssets($empId);
        $leaves    = $this->employeeModel->getEmployeeLeaves($empId);

        $data = [
            'title' => 'الملف الشامل: ' . $employee->full_name,
            'employee' => $employee,
            'documents' => $documents,
            'assets' => $assets,
            'leaves' => $leaves,
            'breadcrumb' => [['label' => 'الموظفين', 'url' => 'employee/index'], ['label' => 'ملف الموظف', 'url' => '#']]
        ];
        
        ob_start(); $this->view('employee/show', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'employee_number'      => trim($_POST['employee_number'] ?? 'EMP-'.time()),
                'full_name'            => trim($_POST['full_name'] ?? ''),
                'name_ar'              => trim($_POST['name_ar'] ?? ''),
                'name_en'              => trim($_POST['name_en'] ?? ''),
                'gender'               => trim($_POST['gender'] ?? 'Male'),
                'dob'                  => trim($_POST['dob'] ?? ''),
                'nationality'          => trim($_POST['nationality'] ?? ''),
                'marital_status'       => trim($_POST['marital_status'] ?? 'Single'),
                'blood_group'          => trim($_POST['blood_group'] ?? ''),
                'personal_email'       => trim($_POST['personal_email'] ?? ''),
                'personal_mobile'      => trim($_POST['personal_mobile'] ?? ''),
                'emergency_contact'    => trim($_POST['emergency_contact'] ?? ''),
                'emergency_relation'   => trim($_POST['emergency_relation'] ?? ''),
                'emergency_phone'      => trim($_POST['emergency_phone'] ?? ''),
                'job_title'            => trim($_POST['job_title'] ?? ''),
                'position'             => trim($_POST['position'] ?? ''),
                'employee_category'    => trim($_POST['employee_category'] ?? ''),
                'employment_type'      => trim($_POST['employment_type'] ?? 'Full-time'),
                'date_of_joining'      => trim($_POST['date_of_joining'] ?? ''),
                'probation_start_date' => trim($_POST['probation_start_date'] ?? ''),
                'probation_end_date'   => trim($_POST['probation_end_date'] ?? ''),
                'employment_status'    => trim($_POST['employment_status'] ?? 'Active'),
                'work_location'        => trim($_POST['work_location'] ?? ''),
                'cost_center'          => trim($_POST['cost_center'] ?? ''),
                'project_assignment'   => trim($_POST['project_assignment'] ?? ''),
                'employee_grade'       => trim($_POST['employee_grade'] ?? ''),
                'bank_name'            => trim($_POST['bank_name'] ?? ''),
                'account_holder'       => trim($_POST['account_holder'] ?? ''),
                'iban'                 => trim($_POST['iban'] ?? ''),
                'salary_payment_method'=> trim($_POST['salary_payment_method'] ?? 'Bank Transfer'),
                'basic_salary'         => (float)($_POST['basic_salary'] ?? 0),
                'housing_allowance'    => (float)($_POST['housing_allowance'] ?? 0),
                'transport_allowance'  => (float)($_POST['transport_allowance'] ?? 0),
                'other_allowances'     => (float)($_POST['other_allowances'] ?? 0)
            ];

            if (empty($data['full_name'])) {
                $this->setFlash('error', 'الاسم الكامل مطلوب.');
            } else {
                $newId = $this->employeeModel->createEmployee($data);
                if ($newId) {
                    $this->setFlash('success', 'تم تسجيل الموظف بنجاح.');
                    $this->redirect('employee/show/' . $newId);
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات الموظف.');
                }
            }
        }

        $data = [
            'title' => 'إضافة موظف جديد',
            'auto_emp_num' => 'EMP-' . date('ym') . rand(100, 999),
            'breadcrumb' => [['label' => 'الموظفين', 'url' => 'employee/index'], ['label' => 'إضافة', 'url' => '#']]
        ];
        ob_start(); $this->view('employee/create', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('employee/index');
        
        $employee = $this->employeeModel->getEmployeeById((int)$id);
        if (!$employee) {
            $this->setFlash('error', 'الموظف غير موجود.');
            $this->redirect('employee/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'full_name'            => trim($_POST['full_name'] ?? ''),
                'name_ar'              => trim($_POST['name_ar'] ?? ''),
                'name_en'              => trim($_POST['name_en'] ?? ''),
                'gender'               => trim($_POST['gender'] ?? 'Male'),
                'dob'                  => trim($_POST['dob'] ?? ''),
                'nationality'          => trim($_POST['nationality'] ?? ''),
                'marital_status'       => trim($_POST['marital_status'] ?? 'Single'),
                'blood_group'          => trim($_POST['blood_group'] ?? ''),
                'personal_mobile'      => trim($_POST['personal_mobile'] ?? ''),
                'personal_email'       => trim($_POST['personal_email'] ?? ''),
                'emergency_contact'    => trim($_POST['emergency_contact'] ?? ''),
                'emergency_relation'   => trim($_POST['emergency_relation'] ?? ''),
                'emergency_phone'      => trim($_POST['emergency_phone'] ?? ''),
                
                'job_title'            => trim($_POST['job_title'] ?? ''),
                'position'             => trim($_POST['position'] ?? ''),
                'employee_category'    => trim($_POST['employee_category'] ?? ''),
                'employment_type'      => trim($_POST['employment_type'] ?? 'Full-time'),
                'date_of_joining'      => trim($_POST['date_of_joining'] ?? ''),
                'probation_start_date' => trim($_POST['probation_start_date'] ?? ''),
                'probation_end_date'   => trim($_POST['probation_end_date'] ?? ''),
                'employment_status'    => trim($_POST['employment_status'] ?? 'Active'),
                'work_location'        => trim($_POST['work_location'] ?? ''),
                'cost_center'          => trim($_POST['cost_center'] ?? ''),
                'project_assignment'   => trim($_POST['project_assignment'] ?? ''),
                'employee_grade'       => trim($_POST['employee_grade'] ?? ''),
                
                'bank_name'            => trim($_POST['bank_name'] ?? ''),
                'account_holder'       => trim($_POST['account_holder'] ?? ''),
                'iban'                 => trim($_POST['iban'] ?? ''),
                'salary_payment_method'=> trim($_POST['salary_payment_method'] ?? 'Bank Transfer'),
                'basic_salary'         => (float)($_POST['basic_salary'] ?? 0),
                'housing_allowance'    => (float)($_POST['housing_allowance'] ?? 0),
                'transport_allowance'  => (float)($_POST['transport_allowance'] ?? 0),
                'other_allowances'     => (float)($_POST['other_allowances'] ?? 0)
            ];

            if (empty($data['full_name'])) {
                $this->setFlash('error', 'الاسم الكامل مطلوب.');
            } else {
                $this->employeeModel->updateEmployee((int)$id, $data);
                $this->setFlash('success', 'تم تحديث بيانات الموظف بنجاح.');
                $this->redirect('employee/show/' . $id);
                return;
            }
        }

        $data = ['title' => 'تعديل بيانات الموظف', 'employee' => $employee];
        ob_start(); $this->view('employee/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->employeeModel->deleteEmployee((int)$id);
            $this->setFlash('success', 'تم مسح ملف الموظف بنجاح.');
        }
        $this->redirect('employee/index');
    }
}