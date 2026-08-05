<?php
// المسار: app/controllers/TicketController.php

class TicketController extends Controller {
    
    /** @var Ticket كائن المودل الخاص بالتذاكر */
    private Ticket $ticketModel;

    public function __construct() {
        // حماية الوصول
        $this->requireAuth();
        $this->ticketModel = $this->model('Ticket');
    }

    /**
     * عرض قائمة تذاكر الدعم الفني
     */
    public function index(): void {
        $tickets = $this->ticketModel->getAllTickets();
        
        $data = [
            'title' => 'خدمة العملاء (Help Desk)',
            'tickets' => $tickets,
            'breadcrumb' => [
                ['label' => 'خدمة العملاء', 'url' => 'ticket/index']
            ],
            'flash' => $this->getFlash()
        ];
        
        ob_start();
        $this->view('tickets/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * إضافة تذكرة دعم فني جديدة
     */
    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'subject'     => trim($_POST['subject'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'customer_id' => !empty($_POST['customer_id']) ? (int)$_POST['customer_id'] : null,
                'priority'    => trim($_POST['priority'] ?? 'medium'),
                'assigned_to' => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null
            ];

            if (empty($data['subject']) || empty($data['description'])) {
                $this->setFlash('error', 'يرجى إدخال عنوان التذكرة ووصف المشكلة بوضوح.');
                $this->redirect('ticket/create');
            }

            if ($this->ticketModel->createTicket($data)) {
                $this->setFlash('success', 'تم فتح التذكرة بنجاح وجاري العمل عليها.');
                $this->redirect('ticket/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ التذكرة في قاعدة البيانات.');
                $this->redirect('ticket/create');
            }
            
        } else {
            // جلب العملاء والمستخدمين (الموظفين) لربط التذكرة بهم
            $db = Database::getInstance();
            
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = [
                'title' => 'فتح تذكرة دعم فني',
                'customers' => $customers,
                'users' => $users,
                'breadcrumb' => [
                    ['label' => 'خدمة العملاء', 'url' => 'ticket/index'],
                    ['label' => 'تذكرة جديدة', 'url' => '#']
                ],
                'flash' => $this->getFlash()
            ];
            
            ob_start();
            $this->view('tickets/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * تحديث حالة التذكرة
     */
    public function changeStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $ticketId = (int)$id;
            $newStatus = trim($_POST['status'] ?? '');
            
            $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
            
            if (in_array($newStatus, $allowedStatuses)) {
                if ($this->ticketModel->updateStatus($ticketId, $newStatus)) {
                    $this->setFlash('success', 'تم تحديث حالة التذكرة بنجاح.');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التحديث.');
                }
            }
        }
        $this->redirect('ticket/index');
    }
}