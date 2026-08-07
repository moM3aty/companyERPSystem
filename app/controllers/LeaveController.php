<?php
// app/controllers/LeaveController.php

class LeaveController extends Controller {
    
    private Leave $leaveModel;

    public function __construct() {
        $this->requireAuth();
        $this->leaveModel = $this->model('Leave');
    }

    public function index(): void {
        $requests = $this->leaveModel->getAllRequests();
        $isAdmin = Session::hasAnyRole(['admin', 'manager']);
        
        $data = [
            'title' => 'طلبات الإجازات',
            'requests' => $requests,
            'is_admin' => $isAdmin,
            'breadcrumb' => [
                ['label' => 'الموارد البشرية', 'url' => '#'],
                ['label' => 'الإجازات', 'url' => 'leave/index']
            ]
        ];
        
        ob_start();
        $this->view('leave/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id'   => (int)($_POST['employee_id'] ?? 0),
                'leave_type_id' => (int)($_POST['leave_type_id'] ?? 0),
                'start_date'    => trim($_POST['start_date'] ?? ''),
                'end_date'      => trim($_POST['end_date'] ?? ''),
                'reason'        => trim($_POST['reason'] ?? '')
            ];

            if (empty($data['employee_id']) || empty($data['leave_type_id']) || empty($data['start_date'])) {
                $this->setFlash('error', 'يرجى تعبئة كافة الحقول المطلوبة.');
                $this->redirect('leave/create');
            }

            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.');
                $this->redirect('leave/create');
            }

            if ($this->leaveModel->createRequest($data)) {
                $this->setFlash('success', 'تم تقديم طلب الإجازة بنجاح، بانتظار الاعتماد.');
                $this->redirect('leave/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ الطلب.');
                $this->redirect('leave/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            
            $leaveTypes = $this->leaveModel->getLeaveTypes();
            
            $data = [
                'title' => 'تقديم طلب إجازة',
                'employees' => $employees,
                'leave_types' => $leaveTypes,
                'breadcrumb' => [
                    ['label' => 'الإجازات', 'url' => 'leave/index'],
                    ['label' => 'طلب جديد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('leave/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('leave/index');

        $reqId = (int)$id;
        $request = $this->leaveModel->getRequestById($reqId);

        if (!$request || $request->status !== 'pending') {
            $this->setFlash('error', 'لا يمكن تعديل طلب معتمد أو مرفوض.');
            $this->redirect('leave/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'employee_id'   => (int)($_POST['employee_id'] ?? $request->employee_id),
                'leave_type_id' => (int)($_POST['leave_type_id'] ?? $request->leave_type_id),
                'start_date'    => trim($_POST['start_date'] ?? $request->start_date),
                'end_date'      => trim($_POST['end_date'] ?? $request->end_date),
                'reason'        => trim($_POST['reason'] ?? $request->reason)
            ];

            if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
                $this->setFlash('error', 'تاريخ النهاية غير صحيح.');
                $this->redirect('leave/edit/' . $reqId);
            }

            if ($this->leaveModel->updateRequest($reqId, $data)) {
                $this->setFlash('success', 'تم تعديل طلب الإجازة بنجاح.');
                $this->redirect('leave/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء التعديل.');
                $this->redirect('leave/edit/' . $reqId);
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM employees ORDER BY name ASC");
            $employees = $db->resultSet();
            $leaveTypes = $this->leaveModel->getLeaveTypes();
            
            $data = [
                'title' => 'تعديل طلب إجازة',
                'request' => $request,
                'employees' => $employees,
                'leave_types' => $leaveTypes,
                'breadcrumb' => [
                    ['label' => 'الإجازات', 'url' => 'leave/index'],
                    ['label' => 'تعديل الطلب', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('leave/edit', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function approve(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->leaveModel->updateStatus((int)$id, 'approved', Session::getUserId())) {
                $this->setFlash('success', 'تم اعتماد وموافقة طلب الإجازة.');
            }
        }
        $this->redirect('leave/index');
    }

    public function reject(string $id = ''): void {
        $this->requireAnyRole(['admin', 'manager']);
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->leaveModel->updateStatus((int)$id, 'rejected', Session::getUserId())) {
                $this->setFlash('success', 'تم رفض طلب الإجازة بنجاح.');
            }
        }
        $this->redirect('leave/index');
    }

    public function delete(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            if ($this->leaveModel->deleteRequest((int)$id)) {
                $this->setFlash('success', 'تم إلغاء وحذف طلب الإجازة.');
            } else {
                $this->setFlash('error', 'لا يمكن حذف طلب تمت الموافقة عليه أو رفضه.');
            }
        }
        $this->redirect('leave/index');
    }
}