<?php
// المسار: app/controllers/CollectionController.php
require_once 'app/models/SalesCollection.php';
require_once 'app/models/Invoice.php'; // لافتراض وجوده لجلب الفواتير المستحقة

class CollectionController {
    private $collectionModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->collectionModel = new SalesCollection($db);
    }

    public function index() {
        $collections = $this->collectionModel->getAllCollections();
        require 'app/views/collections/index.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'receipt_number' => 'REC-' . time(),
                'invoice_id' => $_POST['invoice_id'],
                'treasury_id' => $_POST['treasury_id'],
                'amount' => $_POST['amount'],
                'collection_date' => $_POST['collection_date'],
                'payment_method' => $_POST['payment_method'],
                'reference' => $_POST['reference'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'created_by' => $_SESSION['user_id'] ?? 1
            ];

            if ($this->collectionModel->addCollection($data)) {
                header("Location: /collections?success=1");
                exit;
            } else {
                $error = "حدث خطأ أثناء تسجيل التحصيل.";
                // إعادة تحميل الواجهة مع الخطأ
            }
        } else {
            // جلب البيانات للدروب داون (الخزائن والفواتير)
            $treasuryStmt = $this->db->query("SELECT id, name FROM treasuries");
            $treasuries = $treasuryStmt->fetchAll(PDO::FETCH_ASSOC);
            
            $invoiceStmt = $this->db->query("SELECT id, invoice_number, total FROM invoices ORDER BY id DESC");
            $invoices = $invoiceStmt->fetchAll(PDO::FETCH_ASSOC);

            require 'app/views/collections/create.php';
        }
    }
}
?>