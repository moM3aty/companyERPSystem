<?php
require_once 'app/models/Treasury.php';

class SalesCollection {
    private $db;
    private $treasuryModel;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
        $this->treasuryModel = new Treasury($this->db);
    }

    public function getAllCollections() {
        $stmt = $this->db->query("
            SELECT c.*, i.invoice_number, t.name as treasury_name 
            FROM sales_collections c
            JOIN invoices i ON c.invoice_id = i.id
            JOIN treasuries t ON c.treasury_id = t.id
            ORDER BY c.collection_date DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCollection($data) {
        try {
            $this->db->beginTransaction();

            // 1. تسجيل التحصيل
            $stmt = $this->db->prepare("INSERT INTO sales_collections (receipt_number, invoice_id, treasury_id, amount, collection_date, payment_method, reference, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['receipt_number'],
                $data['invoice_id'],
                $data['treasury_id'],
                $data['amount'],
                $data['collection_date'],
                $data['payment_method'],
                $data['reference'],
                $data['notes'],
                $data['created_by']
            ]);

            // 2. تحديث الخزنة مباشرة من خلال موديول الخزائن
            $treasuryData = [
                'treasury_id' => $data['treasury_id'],
                'transaction_type' => 'receipt',
                'amount' => $data['amount'],
                'transaction_date' => $data['collection_date'],
                'reference' => 'تحصيل فاتورة مبيعات - ' . $data['receipt_number'],
                'description' => $data['notes'],
                'created_by' => $data['created_by']
            ];
            
            $this->treasuryModel->addTransaction($treasuryData);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>