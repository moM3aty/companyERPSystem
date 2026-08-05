<?php
require_once 'app/models/Treasury.php';

class TreasuryController {
    private $treasuryModel;

    public function __construct($db) {
        $this->treasuryModel = new Treasury($db);
    }

    public function index() {
        $treasuries = $this->treasuryModel->getAllTreasuries();
        require 'app/views/treasury/index.php';
    }

    public function show($id) {
        $treasury = $this->treasuryModel->getTreasuryById($id);
        $transactions = $this->treasuryModel->getTransactions($id);
        require 'app/views/treasury/show.php';
    }

    public function createTransaction() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'treasury_id' => $_POST['treasury_id'],
                'transaction_type' => $_POST['transaction_type'],
                'amount' => $_POST['amount'],
                'transaction_date' => $_POST['transaction_date'],
                'reference' => $_POST['reference'] ?? null,
                'description' => $_POST['description'] ?? null,
                'created_by' => $_SESSION['user_id'] ?? 1
            ];

            if ($this->treasuryModel->addTransaction($data)) {
                header("Location: /treasury/show/" . $data['treasury_id'] . "?success=1");
                exit;
            } else {
                $error = "حدث خطأ أثناء تسجيل الحركة المالية.";
                require 'app/views/treasury/create.php';
            }
        } else {
            $treasuries = $this->treasuryModel->getAllTreasuries();
            require 'app/views/treasury/create.php';
        }
    }
}
?>