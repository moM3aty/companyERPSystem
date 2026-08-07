<?php
// app/controllers/TicketController.php

class TicketController extends Controller {
    
    private Ticket $ticketModel;

    public function __construct() {
        $this->requireAuth();
        $this->ticketModel = $this->model('Ticket');
    }

    public function index(): void {
        $tickets = $this->ticketModel->getAllTickets();
        $stats = $this->ticketModel->getTicketStats(); // دالة أضفناها في التحديث السابق للوحة التذاكر
        
        $data = [
            'title' => 'خدمة العملاء (Help Desk)',
            'tickets' => $tickets,
            'stats' => $stats,
            'breadcrumb' => [['label' => 'خدمة العملاء', 'url' => 'ticket/index']]
        ];
        
        ob_start();
        $this->view('tickets/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

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
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM customers ORDER BY name ASC");
            $customers = $db->resultSet();
            $db->query("SELECT id, name FROM users ORDER BY name ASC");
            $users = $db->resultSet();

            $data = [
                'title' => 'فتح تذكرة شكوى أو دعم فني',
                'customers' => $customers,
                'users' => $users,
                'breadcrumb' => [['label' => 'خدمة العملاء', 'url' => 'ticket/index'], ['label' => 'تذكرة جديدة', 'url' => '#']]
            ];
            
            ob_start();
            $this->view('tickets/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('ticket/index');
        
        $ticketId = (int)$id;
        $ticket = $this->ticketModel->getTicketById($ticketId);
        
        if (!$ticket) {
            $this->setFlash('error', 'التذكرة غير موجودة.');
            $this->redirect('ticket/index');
        }

        // جلب التعليقات والمرفقات الخاصة بالتذكرة
        $db = Database::getInstance();
        $db->query("SELECT tc.*, u.name as user_name 
                    FROM ticket_comments tc 
                    LEFT JOIN users u ON tc.user_id = u.id 
                    WHERE tc.ticket_id = :ticket_id 
                    ORDER BY tc.created_at ASC");
        $db->bind(':ticket_id', $ticketId, PDO::PARAM_INT);
        $comments = $db->resultSet();

        $data = [
            'title' => 'متابعة التذكرة: ' . $ticket->ticket_number,
            'ticket' => $ticket,
            'comments' => $comments,
            'breadcrumb' => [['label' => 'الدعم الفني', 'url' => 'ticket/index'], ['label' => 'متابعة التذكرة', 'url' => '#']]
        ];
        
        ob_start();
        $this->view('tickets/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function addComment(string $ticketId = ''): void {
        if ($this->isPost() && !empty($ticketId) && is_numeric($ticketId)) {
            $comment = trim($_POST['comment'] ?? '');
            $attachmentPath = null;

            // معالجة رفع المرفق إن وجد
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = dirname(APP_ROOT) . '/public/uploads/tickets/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir . $fileName)) {
                    $attachmentPath = '/uploads/tickets/' . $fileName;
                }
            }
            
            if (!empty($comment) || $attachmentPath) {
                $db = Database::getInstance();
                $db->query("INSERT INTO ticket_comments (ticket_id, user_id, comment, attachment_path, created_at) VALUES (:ticket_id, :user_id, :comment, :attach, NOW())");
                $db->bind(':ticket_id', (int)$ticketId, PDO::PARAM_INT);
                $db->bind(':user_id', Session::getUserId(), PDO::PARAM_INT);
                $db->bind(':comment', $comment);
                $db->bind(':attach', $attachmentPath);
                $db->execute();
                
                $this->setFlash('success', 'تم إضافة الرد بنجاح.');
            }
        }
        $this->redirect('ticket/show/' . $ticketId);
    }

    public function changeStatus(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $ticketId = (int)$id;
            $newStatus = trim($_POST['status'] ?? '');
            $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
            
            if (in_array($newStatus, $allowedStatuses)) {
                $this->ticketModel->updateStatus($ticketId, $newStatus);
                $this->setFlash('success', 'تم تحديث حالة التذكرة بنجاح.');
            }
        }
        // العودة إلى نفس الصفحة (في حال تم استدعاؤها من الداخل أو الخارج)
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'show') !== false) {
            $this->redirect('ticket/show/' . $id);
        } else {
            $this->redirect('ticket/index');
        }
    }
}