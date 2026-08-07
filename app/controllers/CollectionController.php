<?php
// app/controllers/CollectionController.php

class CollectionController extends Controller {
    
    private $collectionModel;
    private $treasuryModel;

    public function __construct() {
        $this->requireAuth();
        $this->collectionModel = $this->model('SalesCollection');
        $this->treasuryModel = $this->model('Treasury');
    }

    public function index(): void {
        $collections = $this->collectionModel->getAllCollections();
        
        $data = [
            'title' => 'تحصيلات الفواتير',
            'collections' => $collections,
            'breadcrumb' => [
                ['label' => 'المالية', 'url' => '#'],
                ['label' => 'التحصيلات', 'url' => 'collection/index']
            ]
        ];
        
        ob_start();
        $this->view('collections/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'receipt_number' => 'REC-' . time(),
                'invoice_id' => (int)$_POST['invoice_id'],
                'treasury_id' => (int)$_POST['treasury_id'],
                'amount' => (float)$_POST['amount'],
                'collection_date' => trim($_POST['collection_date']),
                'payment_method' => trim($_POST['payment_method']),
                'reference' => trim($_POST['reference'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
                'created_by' => Session::getUserId()
            ];

            if (empty($data['invoice_id']) || empty($data['treasury_id']) || $data['amount'] <= 0) {
                $this->setFlash('error', 'البيانات المدخلة غير صحيحة، تأكد من الفاتورة والخزنة والمبلغ.');
                $this->redirect('collection/create');
            }

            if ($this->collectionModel->addCollection($data)) {
                $this->setFlash('success', 'تم تسجيل التحصيل وتحديث أرصدة الخزنة بنجاح.');
                $this->redirect('collection/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء تسجيل التحصيل في قاعدة البيانات.');
                $this->redirect('collection/create');
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id, name FROM treasuries");
            $treasuries = $db->resultSet();
            
            $db->query("SELECT id, invoice_number, total_amount as total FROM invoices ORDER BY id DESC");
            $invoices = $db->resultSet();

            $data = [
                'title' => 'تسجيل تحصيل جديد',
                'treasuries' => $treasuries,
                'invoices' => $invoices,
                'breadcrumb' => [
                    ['label' => 'التحصيلات', 'url' => 'collection/index'],
                    ['label' => 'جديد', 'url' => '#']
                ]
            ];

            ob_start();
            $this->view('collections/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }
}