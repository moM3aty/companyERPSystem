<?php
// app/controllers/HelpDeskController.php

class HelpDeskController extends Controller {
    
    public function __construct() {
        $this->requireAuth();
    }

    // عرض قائمة التذاكر
    public function index() {
        $ticketModel = $this->model('Ticket');
        
        $data = [
            'title' => 'خدمة العملاء والدعم الفني',
            'tickets' => $ticketModel->getAllTickets(),
            'stats' => $ticketModel->getTicketStats(),
            'flash' => $this->getFlash()
        ];
        
        $this->view('helpdesk/index', $data);
    }

    // صفحة إنشاء تذكرة جديدة
    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'ticket_number' => 'TKT-' . date('Ymd') . '-' . rand(100, 999),
                'subject'       => trim($_POST['subject'] ?? ''),
                'description'   => trim($_POST['description'] ?? ''),
                'customer_id'   => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'priority'      => $_POST['priority'] ?? 'medium',
                'status'        => 'open',
                'created_by'    => $_SESSION['user_id']
            ];

            if (empty($data['subject']) || empty($data['customer_id'])) {
                $this->setFlash('error', 'يجب إدخال عنوان التذكرة واختيار العميل');
                $this->redirect('helpdesk/create');
            }

            $ticketModel = $this->model('Ticket');
            if ($ticketModel->createTicket($data)) {
                $this->setFlash('success', 'تم فتح التذكرة بنجاح برقم ' . $data['ticket_number']);
                $this->redirect('helpdesk/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ التذكرة');
                $this->redirect('helpdesk/create');
            }
        } else {
            // جلب العملاء لملء القائمة المنسدلة
            $customerModel = $this->model('Customer');
            $data = [
                'title' => 'فتح تذكرة دعم فني',
                'customers' => $customerModel->getCustomers(),
                'flash' => $this->getFlash()
            ];
            $this->view('helpdesk/create', $data);
        }
    }

    // عرض تفاصيل التذكرة (تجنبنا استخدام كلمة view لتفادي التعارض)
    public function show($id) {
        $ticketModel = $this->model('Ticket');
        $ticket = $ticketModel->getTicketById($id);
        
        if (!$ticket) {
            $this->setFlash('error', 'التذكرة المطلوبة غير موجودة');
            $this->redirect('helpdesk/index');
        }

        $data = [
            'title' => 'تفاصيل التذكرة #' . $ticket->ticket_number,
            'ticket' => $ticket,
            'flash' => $this->getFlash()
        ];
        
        $this->view('helpdesk/show', $data);
    }

    // تحديث حالة التذكرة
    public function updateStatus($id) {
        if ($this->isPost()) {
            $status = $_POST['status'] ?? 'open';
            $assignedTo = !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null;
            
            $ticketModel = $this->model('Ticket');
            if ($ticketModel->updateTicketStatus($id, $status, $assignedTo)) {
                $this->setFlash('success', 'تم تحديث حالة التذكرة بنجاح');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التحديث');
            }
        }
        $this->redirect('helpdesk/show/' . $id);
    }
}