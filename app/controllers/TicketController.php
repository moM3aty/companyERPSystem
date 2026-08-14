<?php
// app/controllers/TicketController.php

class TicketController extends Controller {
    
    private $ticketModel;

    public function __construct() {
        $this->requireAuth();
        $this->ticketModel = $this->model('Ticket');
    }

    public function index() {
        if (in_array(Session::getUserRole(), ['admin', 'super_admin'])) {
            $tickets = $this->ticketModel->getAllTickets();
        } else {
            $tickets = $this->ticketModel->getMyTickets(Session::getUserId());
        }
        
        $data = [
            'title' => 'تذاكر الدعم الفني',
            'tickets' => $tickets,
            'breadcrumb' => [
                ['label' => 'الدعم', 'url' => '#'],
                ['label' => 'التذاكر', 'url' => 'ticket/index']
            ]
        ];
        
        ob_start();
        $this->view('tickets/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'subject' => trim($_POST['subject'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'priority' => trim($_POST['priority'] ?? 'medium')
            ];

            if (empty($data['subject']) || empty($data['description'])) {
                $this->setFlash('error', 'يرجى إدخال الموضوع والتفاصيل.');
            } else {
                if ($this->ticketModel->createTicket($data)) {
                    $this->setFlash('success', 'تم فتح التذكرة بنجاح، سيقوم الدعم بالرد قريباً.');
                    $this->redirect('ticket/index');
                    return;
                }
            }
        }
        $data = ['title' => 'فتح تذكرة جديدة'];
        ob_start();
        $this->view('tickets/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function updateStatus($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $status = $_POST['status'] ?? 'open';
            $this->ticketModel->updateStatus((int)$id, $status);
            $this->setFlash('success', 'تم تحديث حالة التذكرة.');
        }
        $this->redirect('ticket/index');
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {
            $this->ticketModel->deleteTicket((int)$id);
            $this->setFlash('success', 'تم مسح التذكرة.');
        }
        $this->redirect('ticket/index');
    }
    public function edit($id = '') {
        if (empty($id) || !is_numeric($id)) $this->redirect('ticket/index');
        
        $ticket = $this->ticketModel->getTicketById((int)$id);
        
        // التحقق من أن المستخدم يملك التذكرة أو أنه أدمن
        if (!$ticket || ($ticket->user_id != Session::getUserId() && !in_array(Session::getUserRole(), ['admin', 'super_admin']))) {
            $this->setFlash('error', 'التذكرة غير موجودة أو لا تملك صلاحية تعديلها.');
            $this->redirect('ticket/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'subject' => trim($_POST['subject'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'priority' => trim($_POST['priority'] ?? 'medium')
            ];

            if (empty($data['subject']) || empty($data['description'])) {
                $this->setFlash('error', 'يرجى إدخال الموضوع والتفاصيل.');
            } else {
                if ($this->ticketModel->updateTicket((int)$id, $data)) {
                    $this->setFlash('success', 'تم تعديل التذكرة بنجاح.');
                    $this->redirect('ticket/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                }
            }
        }

        $data = ['title' => 'تعديل التذكرة', 'ticket' => $ticket];
        ob_start(); $this->view('tickets/edit', $data); $content = ob_get_clean();
        Layout::render($content, $data);
    }
}