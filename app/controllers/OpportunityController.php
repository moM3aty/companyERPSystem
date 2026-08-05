<?php
class OpportunityController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $opportunityModel = $this->model('Opportunity');
        $opportunities = $opportunityModel->getAll('id', 'DESC');
        
        // جلب أسماء العملاء والمسؤولين
        $db = Database::getInstance();
        foreach ($opportunities as $opp) {
            $db->query('SELECT name FROM customers WHERE id = :cid');
            $db->bind(':cid', $opp->customer_id);
            $customer = $db->single();
            $opp->customer_name = $customer ? $customer->name : '—';
        }
        
        $data = [
            'title' => 'فرص البيع',
            'opportunities' => $opportunities,
            'flash' => $this->getFlash()
        ];
        $this->view('opportunity/index', $data);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'customer_id' => (int) $_POST['customer_id'],
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']),
                'stage' => $_POST['stage'],
                'estimated_value' => (float) $_POST['estimated_value'],
                'probability' => (int) $_POST['probability'],
                'expected_close_date' => $_POST['expected_close_date'],
                'assigned_to' => !empty($_POST['assigned_to']) ? (int) $_POST['assigned_to'] : null,
            ];
            
            $opportunityModel = $this->model('Opportunity');
            if ($opportunityModel->create($data)) {
                $this->setFlash('success', 'تم إضافة الفرصة بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الإضافة');
            }
            $this->redirect('opportunity/index');
        } else {
            // جلب العملاء والمستخدمين للاختيار
            $customerModel = $this->model('Customer');
            $customers = $customerModel->getCustomers();
            $userModel = $this->model('User');
            $users = $userModel->getAll(); // تحتاج إضافة getAll في User model
            
            $data = [
                'title' => 'إضافة فرصة جديدة',
                'customers' => $customers,
                'users' => $users,
                'flash' => $this->getFlash()
            ];
            $this->view('opportunity/create', $data);
        }
    }

    public function edit($id) {
        // مشابه للإضافة مع جلب البيانات الحالية
        // ...
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $opportunityModel = $this->model('Opportunity');
            if ($opportunityModel->delete($id)) {
                $this->setFlash('success', 'تم حذف الفرصة');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحذف');
            }
            $this->redirect('opportunity/index');
        }
    }
}