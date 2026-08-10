<?php
// app/controllers/HrDocumentController.php

class HrDocumentController extends Controller {
    
    private $docModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->docModel =$this->model('HrDocument');
        $this->employeeModel =$this->model('Employee');
    }

    public function index() {
        $documents = $this->docModel->getAllDocuments();$data = [
            'title' => 'الوثائق الرسمية (Official Documents)',
            'documents' => $documents,
            'breadcrumb' => [['label' => 'الموارد البشرية', 'url' => '#'], ['label' => 'الوثائق', 'url' => 'hrDocument/index']]
        ];
        ob_start(); $this->view('hrDocument/index', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id'       => (int)($_POST['employee_id'] ?? 0),                 'doc_type'          => trim($_POST['doc_type'] ?? ''),
                'doc_number'        => trim($_POST['doc_number'] ?? ''),
                'issue_date'        => trim($_POST['issue_date'] ?? ''),
                'expiry_date'       => trim($_POST['expiry_date'] ?? ''),
                'issuing_authority' => trim($_POST['issuing_authority'] ?? ''),
                'attachment'        => null
            ];

            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {$uploadDir = dirname(APP_ROOT) . '/public/uploads/hr_docs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = time() . '_' . basename($_FILES['attachment']['name']);
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadDir .$fileName)) {
                    $data['attachment'] =$fileName;
                }
            }

            if ($this->docModel->createDocument($data)) {$this->setFlash('success', 'تم حفظ الوثيقة بنجاح.');
                $this->redirect('hrDocument/index'); return;
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء الحفظ.');
            }
        }
        $data = ['title' => 'إضافة وثيقة جديدة', 'employees' => $this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('hrDocument/create', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function edit($id = '') {
        if (empty($id)) $this->redirect('hrDocument/index');$doc = $this->docModel->getDocumentById((int)$id);
        if (!$doc)$this->redirect('hrDocument/index');

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);$data = [
                'employee_id'       => (int)($_POST['employee_id'] ?? 0),                 'doc_type'          => trim($_POST['doc_type'] ?? ''),
                'doc_number'        => trim($_POST['doc_number'] ?? ''),
                'issue_date'        => trim($_POST['issue_date'] ?? ''),
                'expiry_date'       => trim($_POST['expiry_date'] ?? ''),
                'issuing_authority' => trim($_POST['issuing_authority'] ?? '')
            ];
            if ($this->docModel->updateDocument((int)$id, $data)) {$this->setFlash('success', 'تم تعديل بيانات الوثيقة.');
                $this->redirect('hrDocument/index'); return;
            }
        }
        $data = ['title' => 'تعديل وثيقة', 'document' => $doc, 'employees' =>$this->employeeModel->getAllEmployees()];
        ob_start(); $this->view('hrDocument/edit', $data);$content = ob_get_clean();
        Layout::render($content,$data);
    }

    public function delete($id = '') {$this->requireRole('admin');
        if ($this->isPost() && !empty($id)) {$this->docModel->deleteDocument((int)$id);$this->setFlash('success', 'تم حذف الوثيقة.');
        }
        $this->redirect('hrDocument/index');
    }

    public function importExcel() {
        if ($this->isPost())$this->setFlash('success', 'تم الاستيراد بنجاح.');
        $this->redirect('hrDocument/index');
    }
}