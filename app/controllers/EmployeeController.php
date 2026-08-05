<?php
// app/controllers/EmployeeController.php

class EmployeeController extends Controller {
    
    public function __construct() {
        // التحقق من تسجيل الدخول تلقائياً
        $this->requireAuth();
    }

    // عرض قائمة الموظفين
    public function index() {
        $employeeModel = $this->model('Employee');
        $data = [
            'title' => 'إدارة الموظفين',
            'employees' => $employeeModel->getEmployees(),
            'flash' => $this->getFlash()
        ];
        $this->view('employees/index', $data);
    }

    // إضافة موظف جديد
    public function create() {
        $employeeModel = $this->model('Employee');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'position' => trim($_POST['position']),
                'salary' => trim($_POST['salary']),
                'department_id' => trim($_POST['department_id'])
            ];

            // التحقق من البيانات (اختياري)
            $errors = [];
            if (empty($data['name'])) $errors[] = 'اسم الموظف مطلوب';
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'بريد إلكتروني صحيح مطلوب';
            }
            if (empty($data['salary']) || $data['salary'] <= 0) {
                $errors[] = 'الراتب يجب أن يكون أكبر من صفر';
            }

            if (empty($errors)) {
                if ($employeeModel->addEmployee($data)) {
                    $this->setFlash('success', 'تم إضافة الموظف "' . $data['name'] . '" بنجاح');
                    $this->redirect('employee/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء إضافة الموظف (ربما البريد مكرر)');
                    $this->redirect('employee/create');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
                $this->redirect('employee/create');
            }
            exit();
        } else {
            $data = [
                'title' => 'إضافة موظف جديد',
                'departments' => $employeeModel->getDepartments(),
                'flash' => $this->getFlash()
            ];
            $this->view('employees/create', $data);
        }
    }

    // تعديل بيانات موظف
    public function edit($id) {
        $employeeModel = $this->model('Employee');
        $employee = $employeeModel->getEmployeeById($id);
        
        if (!$employee) {
            $this->setFlash('warning', 'الموظف غير موجود');
            $this->redirect('employee/index');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'phone' => trim($_POST['phone']),
                'position' => trim($_POST['position']),
                'salary' => trim($_POST['salary']),
                'department_id' => trim($_POST['department_id'])
            ];

            // التحقق من البيانات
            $errors = [];
            if (empty($data['name'])) $errors[] = 'اسم الموظف مطلوب';
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'بريد إلكتروني صحيح مطلوب';
            }
            if (empty($data['salary']) || $data['salary'] <= 0) {
                $errors[] = 'الراتب يجب أن يكون أكبر من صفر';
            }

            if (empty($errors)) {
                if ($employeeModel->updateEmployee($data, $id)) {
                    $this->setFlash('success', 'تم تحديث بيانات الموظف "' . $data['name'] . '" بنجاح');
                    $this->redirect('employee/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تعديل البيانات');
                    $this->redirect('employee/edit/' . $id);
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
                $this->redirect('employee/edit/' . $id);
            }
            exit();
        } else {
            $data = [
                'title' => 'تعديل بيانات موظف',
                'employee' => $employee,
                'departments' => $employeeModel->getDepartments(),
                'flash' => $this->getFlash()
            ];
            $this->view('employees/edit', $data);
        }
    }

    // حذف موظف
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('employee/index');
        }

        $employeeModel = $this->model('Employee');
        $employee = $employeeModel->getEmployeeById($id);
        
        if (!$employee) {
            $this->setFlash('warning', 'الموظف غير موجود');
            $this->redirect('employee/index');
        }

        if ($employeeModel->deleteEmployee($id)) {
            $this->setFlash('success', 'تم حذف الموظف "' . $employee->name . '" بنجاح');
        } else {
            $this->setFlash('error', 'حدث خطأ أثناء الحذف');
        }
        
        $this->redirect('employee/index');
    }
}