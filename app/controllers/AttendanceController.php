<?php
class AttendanceController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        // عرض تقرير الحضور لشهر معين (افتراضي الشهر الحالي)
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        $db = Database::getInstance();
        $db->query('
            SELECT a.*, e.name as employee_name
            FROM attendance a
            JOIN employees e ON a.employee_id = e.id
            WHERE MONTH(a.date) = :month AND YEAR(a.date) = :year
            ORDER BY a.date DESC
        ');
        $db->bind(':month', $month);
        $db->bind(':year', $year);
        $records = $db->resultSet();
        
        $data = [
            'title' => 'سجل الحضور',
            'records' => $records,
            'month' => $month,
            'year' => $year,
            'flash' => $this->getFlash()
        ];
        
        $this->view('attendance/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $employeeId = $_POST['employee_id'];
            $date = $_POST['date'];
            $checkIn = $_POST['check_in'];
            $checkOut = $_POST['check_out'] ?? null;
            $status = $_POST['status'];
            
            $db = Database::getInstance();
            // التحقق من عدم التكرار
            $db->query('SELECT id FROM attendance WHERE employee_id = :emp AND date = :date');
            $db->bind(':emp', $employeeId, PDO::PARAM_INT);
            $db->bind(':date', $date);
            $exists = $db->single();
            if ($exists) {
                $this->setFlash('error', 'تم تسجيل حضور هذا الموظف في هذا التاريخ مسبقاً');
                $this->redirect('attendance/index');
            }
            
            $db->query('
                INSERT INTO attendance (employee_id, date, check_in, check_out, status)
                VALUES (:emp, :date, :in, :out, :status)
            ');
            $db->bind(':emp', $employeeId, PDO::PARAM_INT);
            $db->bind(':date', $date);
            $db->bind(':in', $checkIn);
            $db->bind(':out', $checkOut);
            $db->bind(':status', $status);
            
            if ($db->execute()) {
                $this->setFlash('success', 'تم تسجيل الحضور بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التسجيل');
            }
            $this->redirect('attendance/index');
        } else {
            // جلب قائمة الموظفين للاختيار
            $employeeModel = $this->model('Employee');
            $employees = $employeeModel->getEmployees();
            
            $data = [
                'title' => 'تسجيل حضور',
                'employees' => $employees,
                'flash' => $this->getFlash()
            ];
            $this->view('attendance/create', $data);
        }
    }
}